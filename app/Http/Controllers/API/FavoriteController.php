<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Transformers\FavoriteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoriteController extends Controller
{
    /**
     * Show list favorite product
     *
     * @return JsonResponse
     */
    public function index()
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $query = Favorite::query()->where('user_id', $this->user->id);
        return responder()->success($query->paginate(20), new FavoriteTransformer)->respond();
    }

    /**
     * Add new favorite product
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $favorite_product = Favorite::query()
            ->where('user_id', $this->user->id)
            ->where('product_id', '=' ,$request->product_id)->first();
        $user = Favorite::where('user_id', $this->user->id)->first();
        if ($user && !$favorite_product){
            Favorite::create([
                'user_id' => $this->user->id,
                'product_id' => $request->product_id,
            ]);
            return responder()->success()->respond();
        }
        else if($user && $favorite_product){
            return responder()->error('duplicate_product', 'This product is already in your favorite list')->respond();
        }
        else{
            Favorite::create([
                'user_id' => $this->user->id,
                'product_id' => $request->product_id,
            ]);
            return responder()->success()->respond();
        }
    }

    /**
     * Remove favorite product
     *
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function destroy(Request $request, $product)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        Favorite::query()
            ->where('user_id', $this->user->id)
            ->where('product_id', $product)->delete();
        return responder()->success()->respond();
    }
}
