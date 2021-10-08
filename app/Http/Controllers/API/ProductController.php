<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
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
     * store product
     *
     * @param StoreProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request){
        $this->admin = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        $product = Product::create($validated);
        $transform = new ProductCollection(Product::query()->where('id', $product->id)->get());
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
        return $this->successWithData('product created successfully', $transform, 200);
    }

    //show product on id
    public function show($id)
    {
        $product = Product::query()->where('id', $id)->get();
        if (!$product) {
            return $this->fails('sorry, product not found', 400);
        }
        $transform = new ProductCollection($product);
        return $transform;
    }

    /**
     * show feature products
     * @return ProductCollection
     */
    public function getFeatureProduct(){
        $feature_products = new ProductCollection(Product::query()->where('feature', '1')->get());
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
     *
     * @param UpdateProductRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $this->admin = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        $product = Product::query()->where('id', $id)->update($validated);
        $product = new ProductCollection(Product::query()->where('id', $id)->get());
        return $this->successWithData('product updated successfully', $product, 200);
    }

    /**
     * Delete product
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        Product::query()->where('id', $id)->delete();
        return $this->success('Product deleted successfully', 200);
    }
}
