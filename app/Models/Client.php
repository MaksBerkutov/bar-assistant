<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['name', 'phone', 'note'];


    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
