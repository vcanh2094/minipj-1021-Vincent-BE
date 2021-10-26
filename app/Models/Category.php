<?php

namespace App\Models;

use App\Transformers\CategoryTransformer;
use Flugg\Responder\Contracts\Transformable;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements Transformable
{
    protected $fillable = [
        'name',
        'status',
    ];
    public function products(){
        return $this->hasMany(Product::class);
    }
    public function transformer()
    {
        return CategoryTransformer::class;
    }
}
