<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    protected $fillable = ['pool_zone_id', 'type', 'name', 'position_x', 'position_y'];
    
    public function poolZone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PoolZone::class);
    }
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
