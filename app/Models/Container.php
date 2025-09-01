<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, Builder, SoftDeletes};

class Container extends Model
{
    use HasFactory, SoftDeletes;
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
