<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\FiltersByRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes; // أضف هذا

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, FiltersByRole;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function dailyTransactions(): MorphMany
    {
        return $this->morphMany(DailyTransaction::class, 'transactionable');
    }

    public function car(): HasOne
    {
        return $this->hasOne(Car::class, 'driver_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function customsDeclarations()
    {
        return $this->hasMany(CustomsDeclaration::class, 'clearance_office_id');
    }
    public function drivingTips()
    {
        return $this->hasMany(Tip::class, 'driver_id');
    }

    /** جميع الحاويات الخاصة بالمكتب عبر الإقرارات الجمركية */
    public function containers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Container::class,
            CustomsDeclaration::class,
            'clearance_office_id', // FK on customs_declarations -> users.id
            'customs_id',          // FK on containers -> customs_declarations.id
            'id',                  // users.id
            'id'                   // customs_declarations.id
        );
    }
}
