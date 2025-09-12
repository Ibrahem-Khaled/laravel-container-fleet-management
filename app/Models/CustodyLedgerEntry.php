<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustodyLedgerEntry extends BaseModel
{
    protected $fillable = [
        'custody_account_id',
        'direction',
        'amount',
        'currency',
        'occurred_at',
        'reference_type',
        'reference_id',
        'counterparty_user_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'occurred_at' => 'datetime',
    ];

    public const DIR_INCREASE = ['issue', 'income', 'transfer_in'];
    public const DIR_DECREASE = ['return', 'expense', 'transfer_out'];

    /* علاقات */
    public function account(): BelongsTo
    {
        return $this->belongsTo(CustodyAccount::class, 'custody_account_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(User::class, 'counterparty_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* سكوبات مساعدة */
    public function scopeBetween($q, $from, $to)
    {
        return $q->whereBetween('occurred_at', [$from, $to]);
    }

    public function scopeIncomeLike($q)
    {
        return $q->whereIn('direction', self::DIR_INCREASE);
    }

    public function scopeExpenseLike($q)
    {
        return $q->whereIn('direction', self::DIR_DECREASE);
    }
}
