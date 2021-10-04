<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    protected $fillable = [
        'slide_name',
        'slide_status',
    ];

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
