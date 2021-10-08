<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;


class ProductController extends Controller
{
    use RespondsWithHttpStatus;
    protected $user;
    private $products;

    /**
     * show list products
     *
     * @return ProductCollection
     */
    public function index(){
        $products = new ProductCollection(Product::all());
        return $products;
    }

    /**
     * store newly created product
     *
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request){
        $this->admin = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        $product = Product::create($validated);
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
        return $this->successWithData('product created successfully', $product, 200);
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

    /**
     * show feature products
     * @return ProductCollection
     */
    public function getFeatureProduct(){
        $feature_products = new ProductCollection(Product::query()->where('feature', '=', '1')->get());
        return $feature_products;
    }

    /**
     * show on-sale products
     * @return ProductCollection
     */
    public function getSaleProduct(){
        $sale_products = new ProductCollection(Product::query()->where('sale', '<>', '0')->get());
        return $sale_products;
    }

    /**
     * show product by category
     * @param Category $category
     * @return ProductCollection
     */
    public function getProductByCategory($id){
        $category_products = new ProductCollection(Product::query()->where('category_id', '=', $id)->get());
        return $category_products;
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
