<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCollection;
use App\Models\Favorite;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class FavoriteController extends Controller
{
    use RespondsWithHttpStatus;
    public function index(){
        $this->user = JWTAuth::parseToken()->authenticate();
        $products = DB::table('favorites')
            ->join('products', 'favorites.product_id', '=', 'products.id')
            ->join('images', 'products.id', '=', 'imageable_id')
            ->select('favorites.product_id',
                'products.name AS product_name',
                'products.price',
                'products.category_id',
                'products.sale AS discount',
                'products.content',
                'products.feature',
                'images.name AS image_name',
                'images.url AS image_url',
                'images.imageable_type AS image_type'
            )
            ->get();
        return $this->successWithData('Fetched favorite products successfully', $products, 200);
    }

    public function store(Request $request){
        $this->user = JWTAuth::parseToken()->authenticate();
        $favorite = Favorite::create([
            'user_id' => $this->user->id,
            'product_id' => $request->product_id,
        ]);
        return $this->successWithData('Added favorite product successfully', $favorite, 200);
    }
}
