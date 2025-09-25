<?php

namespace App\Models;


class Partner extends BaseModel
{
    protected $fillable = ['user_id', 'name', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function movements()
    {
        return $this->hasMany(PartnerCapitalMovement::class);
    }
    public function allocations()
    {
        return $this->hasMany(ProfitAllocation::class);
    }


    /** رصيد حتى تاريخ محدد (إجمالي الإيداعات - السحوبات) */
    public function balanceUntil($until): float
    {
        $deposits = $this->movements()
            ->where('type', 'deposit')
            ->where('occurred_at', '<=', $until)
            ->sum('amount');

        $withdraws = $this->movements()
            ->where('type', 'withdrawal')
            ->where('occurred_at', '<=', $until)
            ->sum('amount');

        return (float)$deposits - (float)$withdraws;
    }

    /** رصيد نهاية شهر معيّن */
    public function balanceAtMonthEnd(int $year, int $month): float
    {
        $end = now()->setDate($year, $month, 1)->endOfMonth()->endOfDay();
        return $this->balanceUntil($end);
    }

    /** الرصيد الحالي الآن */
    public function currentBalance(): float
    {
        return $this->balanceUntil(now());
    }

    /** حساب الأرباح المتاحة من اليومية للشريك */
    public function getAvailableProfitFromDailyTransactions($year = null, $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $startDate = now()->setDate($year, $month, 1)->startOfMonth();
        $endDate = now()->setDate($year, $month, 1)->endOfMonth();

        // جلب المعاملات اليومية للمستخدم المرتبط بالشريك
        $dailyTransactions = \App\Models\DailyTransaction::where('transactionable_type', \App\Models\User::class)
            ->where('transactionable_id', $this->user_id)
            ->where('type', 'income')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalIncome = $dailyTransactions->sum('total_amount');

        // حساب نسبة الشريك من إجمالي رؤوس الأموال
        $totalCapital = \App\Models\Partner::where('is_active', true)->get()->sum(function ($partner) {
            return $partner->currentBalance();
        });

        $partnerPercentage = $totalCapital > 0 ? ($this->currentBalance() / $totalCapital) * 100 : 0;

        // حساب الأرباح المستحقة للشريك
        $partnerProfitShare = ($totalIncome * $partnerPercentage) / 100;

        return [
            'total_income' => $totalIncome,
            'partner_percentage' => $partnerPercentage,
            'partner_profit_share' => $partnerProfitShare,
            'period' => [
                'year' => $year,
                'month' => $month,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ];
    }

    /** حساب المبلغ المسموح بسحبه بناءً على الأرباح ورأس المال */
    public function getWithdrawalLimits($year = null, $month = null): array
    {
        $profitData = $this->getAvailableProfitFromDailyTransactions($year, $month);
        $currentBalance = $this->currentBalance();

        // المبلغ المسموح بسحبه من الأرباح فقط
        $maxProfitWithdrawal = $profitData['partner_profit_share'];

        // المبلغ المسموح بسحبه من رأس المال (مع تحذير)
        $maxCapitalWithdrawal = $currentBalance;

        // إجمالي المبلغ المسموح بسحبه
        $maxTotalWithdrawal = $maxProfitWithdrawal + $maxCapitalWithdrawal;

        return [
            'max_profit_withdrawal' => $maxProfitWithdrawal,
            'max_capital_withdrawal' => $maxCapitalWithdrawal,
            'max_total_withdrawal' => $maxTotalWithdrawal,
            'current_balance' => $currentBalance,
            'profit_data' => $profitData,
            'warnings' => [
                'capital_withdrawal_warning' => 'سحب من رأس المال سيقلل من حصتك في الأرباح المستقبلية',
                'exceed_profit_warning' => 'المبلغ المطلوب سحب أكثر من أرباحك المتاحة'
            ]
        ];
    }
}
