<?php

namespace App\Models;

use App\Transformers\SlideTransformer;
use Flugg\Responder\Contracts\Transformable;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model implements Transformable
{
    protected $fillable = [
        'name',
        'status',
    ];

    public function images()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function transformer(){
        return SlideTransformer::class;
    }
}
