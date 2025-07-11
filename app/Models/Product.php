<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'barcode', 'type','purchase_price', 'stock_quantity', 'price', 'photo'];

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
}
