<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomsDeclaration extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function clearanceOffice()
    {
        return $this->belongsTo(User::class, 'clearance_office_id');
    }

    public function dailyTransactions()
    {
        return $this->morphMany(DailyTransaction::class, 'transactionable');
    }

    public function containers()
    {
        return $this->hasMany(Container::class, 'customs_id');
    }
}
