<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContainerTransferOrder extends BaseModel
{
    protected $fillable = ['container_id', 'price', 'note', 'ordered_at'];

    protected $casts = [
        'price'      => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }
}
