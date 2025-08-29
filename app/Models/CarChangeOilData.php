<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarChangeOilData extends Model
{

    protected $table = 'car_change_oil_data'; // مهم جداً

    protected $fillable = [
        'car_id',
        'km_before',
        'km_after',
        'date',
    ];
    protected $casts = [
        'date' => 'date',
    ];

    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    // نطاقات البحث
    public function scopeHealthy($q)
    {
        $ocTable = (new CarChangeOilData)->getTable(); // = 'car_change_oil_data'
        return $q->whereHas('lastOilChange')
            ->whereRaw("(oil_change_number - GREATEST(0, odometer - (SELECT km_before FROM {$ocTable} oc WHERE oc.car_id = cars.id ORDER BY date DESC, id DESC LIMIT 1))) > 500");
    }

    public function scopeDueSoon($q)
    {
        $ocTable = (new CarChangeOilData)->getTable();
        return $q->whereHas('lastOilChange')
            ->whereRaw("(oil_change_number - GREATEST(0, odometer - (SELECT km_before FROM {$ocTable} oc WHERE oc.car_id = cars.id ORDER BY date DESC, id DESC LIMIT 1))) BETWEEN 1 AND 500");
    }

    public function scopeOverdue($q)
    {
        $ocTable = (new CarChangeOilData)->getTable();
        return $q->whereHas('lastOilChange')
            ->whereRaw("(oil_change_number - GREATEST(0, odometer - (SELECT km_before FROM {$ocTable} oc WHERE oc.car_id = cars.id ORDER BY date DESC, id DESC LIMIT 1))) < 1");
    }
}
