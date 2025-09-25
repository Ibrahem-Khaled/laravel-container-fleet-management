<?php

namespace App\Models;

use App\Traits\FiltersByRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;                   // Trait
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements AuditableContract
{
    use HasFactory, Notifiable, SoftDeletes, FiltersByRole, Auditable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'role_id',
        'is_active',
        'tax_enabled',
        'email_verified_at',
        'password',
        'operational_number',
        'salary',
    ];

    protected $hidden = ['password', 'remember_token'];

    // Spatie Activitylog options على مستوى User
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logExcept(['password', 'remember_token', 'updated_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $ev) => static::class . " {$ev}");
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'salary'            => 'decimal:2',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (is_null($user->salary)) {
                $user->salary = 0;
            }
        });
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

    // كل يوزر له سجل شريك واحد
    public function partner()
    {
        return $this->hasOne(Partner::class);
    }

    public function customsDeclarations()
    {
        return $this->hasMany(CustomsDeclaration::class, 'clearance_office_id');
    }

    public function taxHistory()
    {
        return $this->hasMany(OfficeTaxHistory::class, 'office_id');
    }

    public function drivingTips()
    {
        return $this->hasMany(Tip::class, 'driver_id');
    }

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


    public function getImageAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        } else {
            return asset('assets/images/auth/user.jpg');
        }
    }

    /**
     * التحقق من تفعيل الضرائب للمكتب الجمركي
     */
    public function isTaxEnabled(): bool
    {
        return $this->tax_enabled ?? true;
    }

    /**
     * التحقق من أن المستخدم مكتب تخليص جمركي
     */
    public function isClearanceOffice(): bool
    {
        return $this->role && $this->role->name === 'clearance_office';
    }
}
