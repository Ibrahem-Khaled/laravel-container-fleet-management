<?php

namespace App\Models;


class ProfitAllocation extends BaseModel
{
    protected $fillable = [
        'profit_run_id',
        'partner_id',
        'weight_capital_days',
        'share_amount',
        'avg_balance_during_period'
    ];
    protected $casts = [
        'weight_capital_days' => 'decimal:8',
        'share_amount' => 'decimal:4',
        'avg_balance_during_period' => 'decimal:4',
    ];

    public function run()
    {
        return $this->belongsTo(ProfitRun::class, 'profit_run_id');
    }
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
