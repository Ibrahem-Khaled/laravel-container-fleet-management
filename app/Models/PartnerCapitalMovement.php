<?php

namespace App\Models;


class PartnerCapitalMovement extends BaseModel
{
    protected $fillable = ['partner_id', 'type', 'amount', 'occurred_at', 'notes'];
    protected $casts = ['amount' => 'decimal:4', 'occurred_at' => 'datetime'];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}
