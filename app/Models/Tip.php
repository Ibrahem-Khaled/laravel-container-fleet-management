<?php

namespace App\Models;

class Tip extends BaseModel
{
    protected $fillable = ['price', 'container_id', 'driver_id', 'car_id', 'type'];

    public function container()
    {
        return $this->belongsTo(Container::class, 'container_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    // سكوبات مفيدة (اختيارية)
    public function scopeForDriver($q, $driverId)
    {
        return $q->where('driver_id', $driverId);
    }
    public function scopeBetween($q, $from, $to)
    {
        return $q->whereBetween('created_at', [$from, $to]);
    }
}
