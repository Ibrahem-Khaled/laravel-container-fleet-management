<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DailyTransaction extends Model
{
    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'type',
        'amount',
        'method',
        'tax_value',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }


    public function scopeWithinDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        return $query->when($startDate, function ($query, $date) {
            return $query->whereDate('created_at', '>=', $date);
        })->when($endDate, function ($query, $date) {
            return $query->whereDate('created_at', '<=', $date);
        });
    }

    public function getTransactionsSummary(?string $startDate = null, ?string $endDate = null): array
    {
        // نستخدم العلاقة مع الـ Scope الذي أنشأناه
        $summary = $this->dailyTransactions()
            ->withinDateRange($startDate, $endDate) // تطبيق فلتر التاريخ
            ->select(
                // استخدام DB::raw لحساب المجموع بشكل شرطي في استعلام واحد
                DB::raw("SUM(CASE WHEN type = 'income' THEN total_amount ELSE 0 END) as total_income"),
                DB::raw("SUM(CASE WHEN type = 'expense' THEN total_amount ELSE 0 END) as total_expense")
            )
            ->first();

        $totalIncome = (float)($summary->total_income ?? 0);
        $totalExpense = (float)($summary->total_expense ?? 0);

        return [
            'total_income'  => $totalIncome,
            'total_expense' => $totalExpense,
            'balance'       => $totalIncome - $totalExpense,
        ];
    }
}
