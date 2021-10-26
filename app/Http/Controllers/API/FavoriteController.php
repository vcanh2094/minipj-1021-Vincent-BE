<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoriteController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Show list favorite product
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $products = Favorite::query()
            ->join('products', 'favorites.product_id', '=', 'products.id')
            ->join('images', 'products.id', '=', 'imageable_id')
            ->select('favorites.product_id',
                'products.name AS product_name',
                'products.price',
                'products.category_id',
                'products.sale AS discount',
                'products.content',
                'products.feature',
                'images.url AS image_url',
                'images.imageable_type AS image_type'
            )
            ->where('favorites.user_id', $this->user->id)
            ->get();
        return $this->successWithData('Fetched favorite products successfully', $products, 200);
    }

    /**
     * Add new favorite product
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
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
            return $this->success('Added favorite product successfully', 200);
        }
        else if($user && $favorite_product){
            return $this->fails('This product is already in your favorite list');
        }
        else{
            Favorite::create([
                'user_id' => $this->user->id,
                'product_id' => $request->product_id,
            ]);
            return $this->success('Added favorite product successfully', 200);
        }
    }

    /**
     * Remove favorite product
     *
     * @param Request $request
     * @param $product
     * @return JsonResponse
     */
    public function destroy(Request $request, $product): JsonResponse
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        Favorite::query()
            ->where('user_id', $this->user->id)
            ->where('product_id', '=' ,$product)->delete();
        return $this->success('Deleted favorite product successfully', 200);
    }
}
