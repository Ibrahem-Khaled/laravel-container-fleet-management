<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

class SellAndBuy extends BaseModel
{
    protected $fillable = [
        'title',
        'type',
        'price',
        'parent_id',
    ];

    /**
     * Get all of the model's financial transactions.
     */
    public function transactions(): MorphMany
    {
        // اسم العلاقة هو 'transactionable' كما حددناه في جدول اليومية
        return $this->morphMany(DailyTransaction::class, 'transactionable');
    }

    // يمكنك إضافة العلاقة مع النفس هنا إذا أردت
    public function parent()
    {
        return $this->belongsTo(SellAndBuy::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SellAndBuy::class, 'parent_id');
    }
}
