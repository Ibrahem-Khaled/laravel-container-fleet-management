<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class CashCount extends BaseModel
{

    protected $fillable = [
        'custody_account_id',
        'counted_by',
        'counted_at',
        'total_expected',
        'total_counted',
        'difference',
        'status',
        'notes'
    ];

    protected $casts = [
        'counted_at'     => 'datetime',
        'total_expected' => 'decimal:2',
        'total_counted'  => 'decimal:2',
        'difference'     => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(CustodyAccount::class);
    }
    public function counter()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }
}
