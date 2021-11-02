<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request){
        $products = Product::query();
        return responder()->success($products, new ProductTransformer)->respond();
    }
}
