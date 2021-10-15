<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'product_quantity'
    ];
    public function order(){
        return $this->belongsTo(Order::class);
    }
    public function products(){
        return $this->hasMany(Product::class);
    }
}
