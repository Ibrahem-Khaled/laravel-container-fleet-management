<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class CustodyAccount extends BaseModel
{
    protected $fillable = [
        'user_id',
        'opening_balance',
        'status',
        'opened_by',
        'closed_by',
        'opened_at',
        'closed_at',
        'notes'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }
    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function entries()
    {
        return $this->hasMany(CustodyLedgerEntry::class);
    }
    public function cashCounts()
    {
        return $this->hasMany(CashCount::class);
    }

    // اليومية التي صُرفت/وُردت من/إلى هذه العهدة (ربط مباشر بلا مورف)
    public function dailyTransactions()
    {
        return $this->hasMany(DailyTransaction::class, 'custody_account_id');
    }

    // الرصيد الحالي محسوب من اليومية المرتبطة بالعهدة
    public function currentBalance(): float
    {
        $income  = (float) $this->dailyTransactions()->where('type', 'income')->sum('total_amount');
        $expense = (float) $this->dailyTransactions()->where('type', 'expense')->sum('total_amount');
        return round((float)$this->opening_balance + $income - $expense, 2);
    }
}
