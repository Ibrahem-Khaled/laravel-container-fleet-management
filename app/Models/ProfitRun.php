<?php

namespace App\Models;

class ProfitRun extends BaseModel
{
    protected $fillable = ['year', 'month', 'net_profit_amount', 'status', 'locked_at'];
    protected $casts = ['net_profit_amount' => 'decimal:4', 'locked_at' => 'datetime'];

    public function allocations()
    {
        return $this->hasMany(ProfitAllocation::class);
    }
}
