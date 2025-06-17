<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['client_id','zone_id','date','prepayment','status','arrived','order_id'];


    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function zone(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }
    public function order() { return $this->belongsTo(Order::class); }

}
