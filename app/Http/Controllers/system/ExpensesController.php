<?php

namespace App\Http\Controllers\system;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tip;
use App\Models\DailyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


class ExpensesController extends Controller
{
    // قائمة الموظفين مع لمحة سريعة (الرصيد التراكمي حتى تاريخه)
    public function index(Request $request)
    {
        $users = User::query()
            ->WithoutRoles(['admin', 'client', 'clearance_office', 'partner', 'super_admin']) // استثني الإداريين والمديرين
            ->whereNotNull('salary')   // لو عندك فلاتر إضافية أضفها
            ->orderBy('name')
            ->paginate(20);

        return view('dashboard.expenses.employees.index', compact('users'));
    }

    // صفحة موظف: تفصيل شهري آخر 12 شهر افتراضيًا (مع إمكانية تغيير المدة عبر query string)
    public function show(Request $request, User $user)
    {
        // 1) بداية ونهاية الفترة: من تاريخ التوظيف (created_at) حتى نهاية الشهر الحالي
        $employmentStart = $user->created_at ? $user->created_at->copy()->startOfMonth()
            : Carbon::now()->startOfYear(); // fallback احترازي
        $periodStart = $employmentStart;
        $periodEnd   = Carbon::now()->endOfMonth();

        // 2) بناء قائمة الأشهر (YYYY-MM) من البداية للنهاية
        // ممكن تستخدم CarbonPeriod، أو توليد يدوي
        $periodMonths = collect();
        $cursor = $periodStart->copy();
        while ($cursor->lte($periodEnd)) {
            $periodMonths->push($cursor->format('Y-m'));
            $cursor->addMonth();
        }

        // 3) إجمالي سحوبات اليومية (expense) مجمعة شهريًا لهذا الموظف
        $withdrawals = DB::table('daily_transactions')
            ->selectRaw("
            DATE_FORMAT(`created_at`, '%Y-%m') as ym,
            SUM(CASE WHEN `type` = 'expense' THEN `amount` ELSE 0 END) as total_withdrawals
        ")
            ->where('transactionable_type', '=', User::class)
            ->where('transactionable_id', '=', $user->id)
            ->whereNull('deleted_at') // لأن SoftDeletes لا يُطبّق تلقائيًا مع DB::table
            ->whereBetween('created_at', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->groupBy('ym')
            ->pluck('total_withdrawals', 'ym');

        // 4) التربات مجمعة شهريًا (من price) لهذا السائق
        $tips = DB::table('tips')
            ->selectRaw("DATE_FORMAT(`created_at`, '%Y-%m') as ym, SUM(`price`) as total_tips")
            ->where('driver_id', $user->id)
            ->whereNull('deleted_at')
            ->whereBetween('created_at', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->groupBy('ym')
            ->pluck('total_tips', 'ym');

        // 5) بناء صفوف التقرير + الرصيد المُرحّل
        $monthlyRows = [];
        $runningBalance = 0.0;

        foreach ($periodMonths as $ym) {
            $salary = (float) $user->salary;                   // راتب شهري ثابت من users.salary
            $w = (float) ($withdrawals[$ym] ?? 0);             // سحوبات اليومية
            $t = (float) ($tips[$ym] ?? 0);                    // التربات
            $net = $salary - $w + $t;                          // صافي الشهر
            $runningBalance += $net;                           // رصيد مرحّل

            $monthlyRows[] = [
                'ym'          => $ym,
                'salary'      => $salary,
                'withdrawals' => $w,
                'tips'        => $t,
                'net'         => $net,
                'carry'       => $runningBalance,
            ];
        }

        // 6) إجماليات الفترة
        $totals = [
            'salary'      => array_sum(array_column($monthlyRows, 'salary')),
            'withdrawals' => array_sum(array_column($monthlyRows, 'withdrawals')),
            'tips'        => array_sum(array_column($monthlyRows, 'tips')),
            'net'         => array_sum(array_column($monthlyRows, 'net')),
            'carry'       => $runningBalance,
        ];

        return view('dashboard.expenses.employees.show', compact('user', 'monthlyRows', 'totals', 'periodStart', 'periodEnd'));
    }

    public function driverTipsReport(Request $request, User $user)
    {
        // نطاق التاريخ: افتراضيًا الشهر الحالي فقط
        $from = $request->date('from') ?: Carbon::now()->startOfMonth();
        $to   = $request->date('to')   ?: Carbon::now()->endOfMonth();

        // فلاتر اختيارية
        $containerId = $request->integer('container_id');
        $carId       = $request->integer('car_id');
        $type        = $request->string('type')->toString(); // مثلا: empty / غيره عندك

        // الاستعلام الأساسي + eager loading لتقليل N+1
        $q = Tip::query()
            ->with([
                'container.customs.client', // تحميل متسلسل
                'car:id,number',     // غيّر "number" لو اسم عمودك مختلف
            ])
            ->forDriver($user->id)
            ->between($from, $to)
            ->orderByDesc('created_at');

        if ($containerId) {
            $q->where('container_id', $containerId);
        }
        if ($carId) {
            $q->where('car_id', $carId);
        }
        if ($type) {
            $q->where('type', $type);
        }

        // إجمالي السعر (قبل التقسيم لصفحات)
        $totalPrice = (clone $q)->sum('price');  // sum موثّقة في Query Builder
        $tipsCount  = (clone $q)->count();

        // ترقيم صفحات
        $tips = $q->paginate(20)->withQueryString(); // pagination مدمجة مع Eloquent

        return view('dashboard.expenses.employees.tips', compact(
            'user',
            'tips',
            'totalPrice',
            'tipsCount',
            'from',
            'to',
            'containerId',
            'carId',
            'type'
        ));
    }
}
