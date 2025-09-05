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
}
