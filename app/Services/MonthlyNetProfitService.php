<?php
// app/Services/MonthlyNetProfitService.php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class MonthlyNetProfitService
{
    public function netFor(int $year, int $month): float
    {
        $start = now()->setDate($year, $month, 1)->startOfDay();
        $end   = (clone $start)->endOfMonth();

        $income  = DB::table('daily_transactions')
            ->where('type', 'income')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        $expense = DB::table('daily_transactions')
            ->where('type', 'expense')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');

        return (float)$income - (float)$expense;
    }
}
