<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'payment_method',
        'status',
        ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function order_details(){
        return $this->hasMany(OrderDetail::class);
    }
}
