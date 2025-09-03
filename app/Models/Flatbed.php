<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flatbed extends BaseModel
{
    use HasFactory;

    protected $guarded = ['id'];

    public function containers()
    {
        return $this->belongsToMany(Container::class, 'flatbed_containers', 'flatbed_id', 'container_id');
    }
}
