<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Builder, SoftDeletes};
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Container extends BaseModel
{
    use HasFactory, SoftDeletes;
    protected $appends = ['transfer_price_sum']; // يظهر في JSON تلقائيًا
    protected $fillable = [
        'rent_id',
        'customs_id',
        'is_rent',
        'number',
        'size',
        'price',
        'rent_price',
        'status',
        'transfer_date',
        'date_empty',
        'direction'
    ];

    protected $casts = [
        'is_rent' => 'boolean',
        'transfer_date' => 'datetime',
        'date_empty' => 'datetime',
        'price' => 'integer',
        'rent_price' => 'integer',
    ];
    public function rentUser()
    {
        return $this->belongsTo(User::class, 'rent_id');
    }

    public function customs()
    {
        return $this->belongsTo(CustomsDeclaration::class, 'customs_id');
    }

    public function tips()
    {
        return $this->hasMany(Tip::class);
    }

    public function transferOrders(): HasMany
    {
        return $this->hasMany(ContainerTransferOrder::class);
    }

    public function dailyTransactions()
    {
        return $this->morphMany(DailyTransaction::class, 'transactionable');
    }


    protected function transferPriceSum(): Attribute
    {
        return Attribute::get(function () {
            // 1) موجود كـ attribute من withSum؟
            if (array_key_exists('transfer_price_sum', $this->attributes)) {
                return (float) $this->attributes['transfer_price_sum'];
            }

            // 2) العلاقة محمّلة مسبقًا؟
            if ($this->relationLoaded('transferOrders')) {
                return (float) $this->transferOrders->sum('price');
            }

            // 3) استعلام أخير (انتبه للأداء في الحشود الكبيرة)
            return (float) $this->transferOrders()->sum('price');
        });
    }
    // سكوبات لتبويب الحالة والبحث
    public function scopeStatus($q, $status = null)
    {
        if ($status && in_array($status, ['wait', 'transport', 'done', 'rent', 'storage'])) {
            $q->where('status', $status);
        }
        return $q;
    }

    public function scopeSearch($q, $term = null)
    {
        if ($term) {
            $q->where(function ($qq) use ($term) {
                $qq->where('number', 'like', "%{$term}%")
                    ->orWhere('direction', 'like', "%{$term}%");
            });
        }
        return $q;
    }

    // الحاويات المحتسبة كـ "تم النقل"
    public function scopeTransported(Builder $q): Builder
    {
        // عدّل الحالات حسب تعريف مشروعك
        return $q->whereIn('status', ['transport', 'done']);
    }

    // فلترة بالتاريخ على transfer_date
    public function scopeWithinTransferDate(Builder $q, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $q->whereDate('transfer_date', '>=', $startDate);
        }
        if ($endDate) {
            $q->whereDate('transfer_date', '<=', $endDate);
        }
        return $q;
    }
}
