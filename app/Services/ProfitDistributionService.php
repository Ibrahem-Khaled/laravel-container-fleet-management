<?php
// app/Services/ProfitDistributionService.php
namespace App\Services;

use App\Models\Partner;
use App\Models\ProfitRun;
use App\Models\ProfitAllocation;
use Illuminate\Support\Facades\DB;

class ProfitDistributionService
{
    /**
     * ينفّذ توزيع أرباح لشهر محدد. لو فيه Run موجودة لنفس (year,month) يرجّعه.
     * @return ProfitRun
     */
    public function runForMonth(int $year, int $month, float $netProfit): ProfitRun
    {
        // لو الربح صفر أو سالب، نسمح بالتوزيع (قد يكون خسارة تُسجل صفر أو تخصم زيرو؟)
        // هنا هنوزع فقط لو > 0. عدّل حسب سياستك:
        if ($netProfit <= 0) {
            // إنشاء Run فارغة مَثلاً
        }

        return DB::transaction(function () use ($year, $month, $netProfit) {

            // احضر أو أنشئ Run
            $run = ProfitRun::firstOrCreate(
                ['year' => $year, 'month' => $month],
                ['net_profit_amount' => $netProfit, 'status' => 'draft']
            );

            // مسح أية تخصيصات قديمة (إعادة التشغيل)
            $run->allocations()->delete();

            // حدود الشهر
            $start = now()->setDate($year, $month, 1)->startOfDay();
            $end   = (clone $start)->endOfMonth()->endOfDay();

            $partners = Partner::query()->where('is_active', true)->get();

            // احسب وزن كل شريك: Capital-Days
            $weights = [];
            foreach ($partners as $p) {
                $weights[$p->id] = $this->capitalDaysForPartner($p->id, $start->copy(), $end->copy());
            }

            $totalWeight = array_sum($weights) ?: 0.0;

            // لو مفيش وزن (مافيش رأس مال فعّال) نخرج بدون تخصيص
            if ($totalWeight <= 0 || $netProfit == 0.0) {
                $run->update(['net_profit_amount' => $netProfit, 'status' => 'locked', 'locked_at' => now()]);
                return $run;
            }

            // أنشئ التخصيصات
            foreach ($partners as $p) {
                $w = $weights[$p->id];
                if ($w <= 0) continue;

                $share = $netProfit * ($w / $totalWeight);
                ProfitAllocation::create([
                    'profit_run_id' => $run->id,
                    'partner_id'    => $p->id,
                    'weight_capital_days'       => $w,
                    'avg_balance_during_period' => $w / (float)$start->daysInMonth, // متوسط رصيد تقريبي
                    'share_amount'  => round($share, 4),
                ]);
            }

            $run->update(['net_profit_amount' => $netProfit, 'status' => 'locked', 'locked_at' => now()]);
            return $run;
        });
    }

    /**
     * يحسب مجموع (الرصيد × الأيام) داخل [start..end] عبر تجزئة الفترات حسب الحركات.
     * - الإيداع يزيد الرصيد من لحظة occurred_at (inclusive) فما بعد.
     * - السحب يقلّل الرصيد من لحظة occurred_at فما بعد.
     * - أي حركات قبل start تؤثر على الرصيد الافتتاحي.
     */
    private function capitalDaysForPartner(int $partnerId, $start, $end): float
    {
        // الرصيد الافتتاحي = مجموع الإيداعات - السحوبات قبل بداية الفترة
        $openingDeposits = DB::table('partner_capital_movements')
            ->where('partner_id', $partnerId)
            ->where('type', 'deposit')
            ->where('occurred_at', '<', $start)
            ->sum('amount');

        $openingWithdraws = DB::table('partner_capital_movements')
            ->where('partner_id', $partnerId)
            ->where('type', 'withdrawal')
            ->where('occurred_at', '<', $start)
            ->sum('amount');

        $balance = (float)$openingDeposits - (float)$openingWithdraws;

        // اجلب الحركات داخل نطاق الفترة مرتّبة
        $moves = DB::table('partner_capital_movements')
            ->where('partner_id', $partnerId)
            ->whereBetween('occurred_at', [$start, $end])
            ->orderBy('occurred_at')
            ->get(['type', 'amount', 'occurred_at']);

        $cursor = $start->copy();
        $weight = 0.0;

        // مرّ على المقاطع البينية: من cursor إلى تاريخ الحركة
        foreach ($moves as $m) {
            $at = \Illuminate\Support\Carbon::parse($m->occurred_at);
            if ($at->greaterThan($end)) break;

            // أضف وزن المقطع الحالي
            $days = $cursor->diffInDays($at) ?: 0; // عدد الأيام الكاملة
            if ($days > 0 && $balance > 0) {
                $weight += $balance * $days;
            }

            // طبّق الحركة على الرصيد من لحظة الحركة
            $delta = (float)$m->amount * ($m->type === 'deposit' ? +1 : -1);
            $balance += $delta;

            // حرّك المؤشر
            $cursor = $at;
        }

        // آخر مقطع حتى نهاية الشهر
        $days = $cursor->diffInDays($end->copy()->addDay()->startOfDay()); // شمول اليوم الأخير
        if ($days > 0 && $balance > 0) {
            $weight += $balance * $days;
        }

        return $weight; // وحدة: (عملة × يوم)
    }
}
