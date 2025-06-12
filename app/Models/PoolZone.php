<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoolZone extends Model
{
    protected $fillable = ['name'];

    public function zones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Zone::class);
    }
}
