<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends BaseModel
{

    protected $fillable = [
        'name',
        'description',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
