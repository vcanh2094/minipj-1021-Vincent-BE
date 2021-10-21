<?php
namespace App\Services;

use App\Models\Image;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function handleUploadProductImage($image, $productID){
        if($image){
            $path = $image->store('images/vcanh', 's3');
            Image::create([
                'name' => basename($path),
                'url' => Storage::disk('s3')->url($path),
                'size' => $image->getSize(),
                'imageable_id' => $productID,
                'imageable_type' => Product::class
            ]);
        }
    }

    public function handleUpdateProductImage($image, $path, $product){
        Image::create([
            'name' => basename($path),
            'url' => Storage::disk('s3')->url($path),
            'size' => $image->getSize(),
            'imageable_id' => $product,
            'imageable_type' => Product::class
        ]);
    }
}
