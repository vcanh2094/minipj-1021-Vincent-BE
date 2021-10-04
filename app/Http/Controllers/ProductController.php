<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    protected $user;

    public function __construct(){
        $this->user = JWTAuth::parseToken()->authenticate();
    }

    //show list products
    public function index(){
        return $this->user->products->get();
    }

    //store a newly created product
    public function store(Request $request){
        //validate
        $data = $request->only('product_name', 'product_price', 'product_content', 'cate_id', 'product_feature', 'product_sale');
        $validator = Validator::make($data, [
            'product_name' => 'required|string',
            'product_price' => 'required',
            'cate_id' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }

        //if request valid, create new product
        $product = $this->user->products()->create([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_content' => $request->product_content,
            'cate_id' => $request->cate_id,
            'product_feature' => $request->product_feature,
            'product_sale' => $request->product_sale,
        ]);

        //product created, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }

    //show product on id
    public function show($id)
    {
        $product = $this->user->products()->find($id);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, product not found.'
            ], 400);
        }
        return $product;
    }

    //show feature product
    public function getFeatureProduct(){
        $feature_products = $this->user->products()->where('product_feature', '=', '1')->get();
        if (!$feature_products) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, there is no feature product.'
            ], 400);
        }
        return $feature_products;
    }

    //show on sale product
    public function getSaleProduct(){
        $sale_products = $this->user->products()->where('product_sale', '<>', '')->get();
        if (!$sale_products) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, dont have any product on sale.'
            ], 400);
        }
        return $sale_products;
    }

    //show product by category
    public function getProductByCategory($id){
        $cate_products = $this->user->products()->where('cate_id', '=', $id)->get();
        if (!$cate_products) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, category not found.'
            ], 400);
        }
        return $cate_products;
    }
}
