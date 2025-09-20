<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class DailyTransaction extends BaseModel
{

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'custody_account_id',
        'type',
        'amount',
        'method',
        'tax_value',
        'total_amount',
        'notes',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'tax_value' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function custodyAccount(): BelongsTo
    {
        return $this->belongsTo(CustodyAccount::class, 'custody_account_id');
    }


    public function scopeWithinDateRange(Builder $q, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $q->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $q->whereDate('created_at', '<=', $endDate);
        }
        return $q;
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




    /* سكوبات مريحة */
    public function scopeYear($q, int $year)
    {
        return $q->whereYear('created_at', $year);
    }

    public function scopeIncome($q)
    {
        return $q->where('type', 'income');
    }

    public function scopeExpense($q)
    {
        return $q->where('type', 'expense');
    }

    public function scopeMethodCash($q)
    {
        return $q->where('method', 'cash');
    }
    public function scopeMethodBank($q)
    {
        return $q->where('method', 'bank');
    }

    // تسمية الفئة حسب الـ morph type
    public function getCategoryNameAttribute(): string
    {
        // استخدم morphMap إن أحببت لتقصير الأسماء في DB
        $map = [
            \App\Models\Car::class            => 'سيارات',
            // \App\Models\User::WithRoles(['driver','employee'])       => 'مرتبات',
            \App\Models\Container::class      => 'حاويات',
            // fallback
        ];

        return $map[$this->transactionable_type] ?? class_basename($this->transactionable_type);
    }
}
