<?php

namespace App\Models;

use App\Transformers\ProductTransformer;
use Flugg\Responder\Contracts\Transformable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model implements Transformable
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'price',
        'content',
        'description',
        'category_id',
        'feature',
        'sale'
    ];
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    public function transformer()
    {
        return ProductTransformer::class;
    }
}
