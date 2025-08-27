<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarChanegOilData extends Model
{
    use HasFactory;

    protected $fillable = [
        'car_id',
        'km_before',
        'km_after',
        'price',
        'date',
    ];

    public function car()
    {
        return $this->belongsTo(Cars::class, 'car_id');
    }
}
