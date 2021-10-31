<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'province',
        'district',
        'ward',
        'street_name',
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
}
