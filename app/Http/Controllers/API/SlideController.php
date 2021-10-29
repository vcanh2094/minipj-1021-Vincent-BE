<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlideRequest;
use App\Models\Slide;
use App\Services\SlideService;
use App\Transformers\SlideTransformer;
use Flugg\Responder\Responder;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;

class SlideController extends Controller
{
    /**
     * Store new slide.
     *
     * @param StoreSlideRequest $request
     * @param SlideService $slideService
     * @return JsonResponse
     */
    public function store(StoreSlideRequest $request, SlideService $slideService)
    {
        JWTAuth::parseToken()->authenticate();
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
    public function show(Responder $responder)
    {
        return $responder->success(Slide::query()->where('status', 1)->orderByDesc('updated_at')->take(5), new SlideTransformer)->respond();
    }
}
