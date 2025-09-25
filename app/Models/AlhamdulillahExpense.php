<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlhamdulillahExpense extends Model
{
    protected $fillable = [
        'year',
        'month',
        'container_count',
        'amount_per_container',
        'total_amount',
        'spent_amount',
        'remaining_amount',
        'notes'
    ];

    protected $casts = [
        'amount_per_container' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'spent_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
    ];

    /**
     * حساب المبلغ المتبقي تلقائياً
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->remaining_amount = $model->total_amount - $model->spent_amount;
        });
    }

    /**
     * الحصول على اسم الشهر بالعربية
     */
    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
            5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
            9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];

        return $months[$this->month] ?? 'غير محدد';
    }
}
