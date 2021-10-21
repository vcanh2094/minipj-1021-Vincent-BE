<?php
namespace App\Services;

use App\Models\Image;
use App\Models\Slide;
use Illuminate\Support\Facades\Storage;

class SlideService
{
    public function handleUploadSlideImage($image, $slideID){
        if($image){
            $path = $image->store('images/vcanh', 's3');
            Image::create([
                'name' => basename($path),
                'url' => Storage::disk('s3')->url($path),
                'size' => $image->getSize(),
                'imageable_id' => $slideID,
                'imageable_type' => Slide::class
            ]);
        }
    }
}
