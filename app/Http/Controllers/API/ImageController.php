<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    public function store (Request $request){
        $path = $request->file('images')->store('images', 's3');
        Storage::disk('s3')->setVisibility($path, 'public');

        $image = Image::create([
           'image_name' => basename($path),
           'image_status' => $request->image_status,
           'image_url' => Storage::disk('s3')->url($path),
           'image_size' => $path->getSize(),
            'disk' => $request->disk,
        ]);

        return $image;
    }

    public function show(Image $image){
        return $image;
    }
}
