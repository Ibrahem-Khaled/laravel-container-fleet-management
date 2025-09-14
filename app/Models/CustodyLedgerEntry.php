<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CustodyLedgerEntry extends BaseModel
{
    protected $fillable = [
        'custody_account_id',
        'daily_transaction_id',
        'direction',
        'amount',
        'currency',
        'occurred_at',
        'reference_id',
        'reference_type',
        'counterparty_user_id',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'amount'      => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(CustodyAccount::class, 'custody_account_id');
    }
    public function dailyTransaction()
    {
        return $this->belongsTo(DailyTransaction::class, 'daily_transaction_id');
    }
    public function counterparty()
    {
        return $this->belongsTo(User::class, 'counterparty_user_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
