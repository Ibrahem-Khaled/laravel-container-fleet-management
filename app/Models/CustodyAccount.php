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
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /* علاقات */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(CustodyLedgerEntry::class);
    }

    public function cashCounts(): HasMany
    {
        return $this->hasMany(CashCount::class);
    }

    /* رصيد لحظي مشتق من دفتر الحركات */
    public function currentBalance(?string $upTo = null): float
    {
        $query = $this->ledgerEntries();

        if ($upTo) {
            $query->where('occurred_at', '<=', $upTo);
        }

        $sumIn  = (clone $query)->whereIn('direction', ['issue', 'income', 'transfer_in'])
            ->sum('amount');
        $sumOut = (clone $query)->whereIn('direction', ['return', 'expense', 'transfer_out'])
            ->sum('amount');
        $adj    = (clone $query)->where('direction', 'adjustment')->sum('amount');

        // opening_balance يؤخذ في الاعتبار كبداية
        return (float) ($this->opening_balance + $sumIn - $sumOut + $adj);
    }

    /* سكوب للحسابات المفتوحة */
    public function scopeOpen($q)
    {
        return $q->where('status', 'open');
    }

    /* سكوب للحسابات المغلقة */
    public function scopeClosed($q)
    {
        return $q->where('status', 'closed');
    }
}
