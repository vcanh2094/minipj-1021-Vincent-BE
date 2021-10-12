<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends Model
{
    use SoftDeletes;
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
