<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'driver_id',
        'type',
        'type_car',
        'model_car',
        'serial_number',
        'license_expire',
        'scan_expire',
        'card_run_expire',
        'number',
        'insurance_expire',
        'oil_change_number'
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function tips()
    {
        return $this->hasMany(Tip::class);
    }
}
