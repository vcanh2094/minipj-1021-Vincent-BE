<?php

namespace App\Models;

use App\Transformers\OrderTransformer;
use Flugg\Responder\Contracts\Transformable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model implements Transformable
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
    public function transformer()
    {
        return OrderTransformer::class;
    }
}
