<?php

namespace App\Models;

use App\Transformers\CategoryTransformer;
use Flugg\Responder\Contracts\Transformable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model implements Transformable
{
    protected $fillable = [
        'cate_name',
        'cate_status',
    ];

    public function products(){
        return $this->hasMany(Product::class);
    }
    public function transformer()
    {
        return CategoryTransformer::class;
    }
}
