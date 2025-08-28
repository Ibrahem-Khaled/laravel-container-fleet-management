<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
