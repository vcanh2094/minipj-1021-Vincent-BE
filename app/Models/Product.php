<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'content',
        'category_id',
        'feature',
        'sale'
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
