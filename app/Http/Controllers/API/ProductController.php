<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;


class ProductController extends Controller
{
    protected $user;
    private $products;

    //show list products
    public function index(){
        $products = DB::table('products')->get();
        return $products;
    }

    //store a newly created product
    public function store(Request $request){
        $this->user = JWTAuth::parseToken()->authenticate();
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
        $product = Product::create([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_content' => $request->product_content,
            'cate_id' => $request->cate_id,
            'product_feature' => $request->product_feature,
            'product_sale' => $request->product_sale,
        ]);

//        $images = $request->file('images');
//        foreach ($images as $image){
//            $path = $request->file('images')->store('images', 's3');
//            Storage::disk('s3')->setVisibility($path, 'public');
//            $image = Image::create([
//                'image_name' => basename($path),
//                'image_status' => $request->image_status,
//                'image_url' => Storage::disk('s3')->url($path),
//                'image_size' => $request->file('images')->getSize(),
//                'disk' => $request->disk,
//                'imageable_id' => $product->product_id,
//                'imageable_type' => Product::class
//            ]);
//        }
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
        $product = DB::table('products')->where('product_id', '=', $id)->get();
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
        $feature_products = DB::table('products')->where('product_feature', '=', '1')->get();
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
        $sale_products = DB::table('products')->where('product_sale', '<>', '')->get();
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
        $cate_products = DB::table('products')->where('cate_id', '=', $id)->get();
        if (!$cate_products) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, category not found.'
            ], 400);
        }
        return $cate_products;
    }

    /**
     * Update product
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        //Validate data
        $data = $request->only('product_name', 'product_price', 'cate_id');
        $validator = Validator::make($data, [
            'product_name' => 'required|string',
            'product_price' => 'required|numeric|gt:0',
            'cate_id' => 'required|numeric|min:1',
        ]);
        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 200);
        }
        //Request is valid, update product
        $product = DB::table('products')->where('product_id', '=', $id)->update([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_content' => $request->product_content,
            'product_feature' => $request->product_feature,
            'product_sale' => $request->product_sale,
            'cate_id' => $request->cate_id,
        ]);
        //Product updated, return success response
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Delete product
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $product = Product::query()->where('product_id', '=', $id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ], Response::HTTP_OK);
    }
}
