<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideRequest;
use App\Http\Resources\SlideCollection;
use App\Models\Image;
use App\Models\Product;
use App\Models\Slide;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class SlideController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * store new slide
     *
     * @param StoreSlideRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSlideRequest $request){
        $this->admin = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        $slide = Slide::create($validated);
        if($request->hasFile('image')){
            $path = $request->file('image')->store('images/vcanh', 's3');
            Image::create([
                'name' => basename($path),
                'status' => $request->img_status,
                'url' => Storage::disk('s3')->url($path),
                'size' => $request->file('image')->getSize(),
                'disk' => $request->disk,
                'imageable_id' => $slide->id,
                'imageable_type' => Slide::class
            ]);
        }
        $transform = new SlideCollection(Slide::query()->where('id', $slide->id)->get());
        return $this->successWithData('slide created successfully', $transform, 200);
    }

    /**
     * show all slides
     *
     * @return SlideCollection
     */
    public function show(){
        $slides = new SlideCollection(Slide::all());
        return $slides;
    }
}
