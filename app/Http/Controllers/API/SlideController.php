<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideRequest;
use App\Models\Image;
use App\Models\Product;
use App\Models\Slide;
use App\Services\SlideService;
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
     * @param SlideService $slideService
     * @return JsonResponse
     */
    public function store(StoreSlideRequest $request, SlideService $slideService): JsonResponse
    {
        $this->admin = JWTAuth::parseToken()->authenticate();
        $slide = Slide::create($request->validated());
        $slideService->handleUploadSlideImage($request->images,$slide->id);
        return responder()->success(Slide::query()->where('id', $slide->id)->get(), new SlideTransformer)->respond();
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
