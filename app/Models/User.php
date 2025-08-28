<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\FiltersByRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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



    ///this accessors functions
    public function getTotalIncome(?string $startDate = null, ?string $endDate = null): float
    {
        return (float) $this->dailyTransactions()
            ->where('type', 'income') // الشرط الإضافي هنا
            ->withinDateRange($startDate, $endDate)
            ->sum('total_amount');
    }

    /**
     * حساب إجمالي المنصرف (Expense) فقط للمستخدم مع فلتر تاريخ اختياري.
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return float
     */
    public function getTotalExpense(?string $startDate = null, ?string $endDate = null): float
    {
        return (float) $this->dailyTransactions()
            ->where('type', 'expense') // الشرط الإضافي هنا
            ->withinDateRange($startDate, $endDate)
            ->sum('total_amount');
    }
}
