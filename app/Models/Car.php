<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Car extends BaseModel
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
        'oil_change_number',
        'odometer',            // عداد السيارة الحالي
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function tips()
    {
        return $this->hasMany(Tip::class);
    }
    public function dailyTransactions()
    {
        return $this->morphMany(DailyTransaction::class, 'transactionable');
    }

    // علاقة كل سيارة بسجلات تغييرات الزيت والقراءات السابقة
    public function oilChanges(): HasMany
    {
        // غيّر اسم جدول/موديل OilChange لو مختلف عندك
        return $this->hasMany(CarChangeOilData::class)->orderBy('date')->orderBy('id');
    }

    // آخر تغيير زيت مثبت (الأحدث بحسب التاريخ ثم المعرف)
    public function lastOilChange(): HasOne
    {
        return $this->hasOne(CarChangeOilData::class)->ofMany(
            ['date' => 'max', 'id' => 'max'],
            function ($q) {
                $q->whereNotNull('date');
            }
        );
    }

    // Accessor: يحسب الباقي تلقائياً
    public function getRemainingKmAttribute(): ?int
    {
        $last = $this->lastOilChange;
        if (!$last) return null; // لا توجد دورة مثبتة بعد

        $sinceChange = max(0, (int)$this->odometer - (int)$last->km_before);
        return (int)$this->oil_change_number - $sinceChange;
    }

    // Accessor: نسبة التقدم نحو الاستحقاق (0..100)
    public function getOilProgressPercentAttribute(): int
    {
        $last = $this->lastOilChange;
        if (!$last || $this->oil_change_number <= 0) return 0;

        $used = max(0, (int)$this->odometer - (int)$last->km_before);
        $pct = (int) round(min(100, ($used / $this->oil_change_number) * 100));
        return $pct;
    }

    // Scopes للحالات

    public function scopeHealthy($query)
    {
        $ocTable = (new CarChangeOilData)->getTable(); // = 'car_change_oil_data'
        return $query->whereHas('lastOilChange')
            ->whereRaw(
                "(oil_change_number - GREATEST(0, odometer - (SELECT km_before
                   FROM {$ocTable} oc
                   WHERE oc.car_id = cars.id
                   ORDER BY date DESC, id DESC LIMIT 1)
                )) > 500"
            );
    }

    public function scopeDueSoon($query)
    {
        $ocTable = (new CarChangeOilData)->getTable();
        return $query->whereHas('lastOilChange')
            ->whereRaw(
                "(oil_change_number - GREATEST(0, odometer - (SELECT km_before
                   FROM {$ocTable} oc
                   WHERE oc.car_id = cars.id
                   ORDER BY date DESC, id DESC LIMIT 1)
                )) BETWEEN 1 AND 500"
            );
    }

    public function scopeOverdue($query)
    {
        $ocTable = (new CarChangeOilData)->getTable();
        return $query->whereHas('lastOilChange')
            ->whereRaw(
                "(oil_change_number - GREATEST(0, odometer - (SELECT km_before
                   FROM {$ocTable} oc
                   WHERE oc.car_id = cars.id
                   ORDER BY date DESC, id DESC LIMIT 1)
                )) < 1"
            );
    }
}
