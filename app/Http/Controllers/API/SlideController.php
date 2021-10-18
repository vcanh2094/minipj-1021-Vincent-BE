<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideRequest;
use App\Models\Image;
use App\Models\Product;
use App\Models\Slide;
use App\Traits\RespondsWithHttpStatus;
use App\Transformers\SlideTransformer;
use Flugg\Responder\Responder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class SlideController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Store new slide.
     *
     * @param StoreSlideRequest $request
     * @param Responder $responder
     * @return JsonResponse
     */
    public function store(StoreSlideRequest $request, Responder $responder): JsonResponse
    {
        $this->admin = JWTAuth::parseToken()->authenticate();
        $slide = Slide::create($request->validated());
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
        return $responder->success(Slide::query()->where('id', $slide->id)->get(), new SlideTransformer)->respond();
    }

    /**
     * Show all slides
     *
     * @param Responder $responder
     * @return JsonResponse
     */
    public function show(Responder $responder): JsonResponse
    {
        return $responder->success(Slide::all()->take(5), new SlideTransformer)->respond();
    }
}
