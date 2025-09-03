<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

abstract class BaseModel extends Model implements AuditableContract
{
    use LogsActivity, Auditable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                               // سجّل كل الخصائص
            ->logExcept(['updated_at', 'remember_token', 'password']) // استثناءات
            ->logOnlyDirty()                         // سجّل الفروقات فقط
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $ev) => static::class . " {$ev}");
    }
}
