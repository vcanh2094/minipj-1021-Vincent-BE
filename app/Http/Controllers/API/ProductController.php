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
    public function index(Request $request){
        $product_query = Product::query()->with(['category', 'images'])
        ->when($request->has('category'), function($query) use ($request){
                return $query->where('category_id', $request->category)
                            ->take(10);
            })
        ->when($request->has('feature'), function ($query){
                return $query->where('feature', 1)
                            ->orderByDesc('id')
                            ->take(8);
            })
        ->when($request->has('sale'), function ($query){
                return $query->where('sale', '>', 0)
                            ->orderByDesc('id')
                            ->take(9);
        })
        ->when($request->has('id'), function ($query) use ($request){
            return $query->where('id', $request->id);
        })
        ->when($request->has('search'), function ($query) use ($request){
            return $query->where('name', 'like','%'.$request->search.'%')
                        ->orderBy('id');
        });
        $products = $product_query->paginate(20);
        $products = (new ProductCollection($products));
        return $products;
    }

    /**
     * store product
     *
     * @param StoreProductRequest $request
     * @param Image $image
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreProductRequest $request, Image $image){
        $this->admin = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        $product = Product::create($validated);
        if($request->hasFile('images')){
                $path = $request->file('images')->store('images/vcanh', 's3');
                Image::create([
                    'name' => basename($path),
                    'status' => $request->status,
                    'url' => Storage::disk('s3')->url($path),
                    'size' => $request->file('images')->getSize(),
                    'disk' => $request->disk,
                    'imageable_id' => $product->id,
                    'imageable_type' => Product::class
                ]);
        }
        $transform = new ProductCollection(Product::query()->where('id', $product->id)->get());
        return $this->successWithData('product created successfully', $transform, 200);
    }

    /**
     * show product detail
     *
     * @param $product
     * @return ProductCollection|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function show($product)
    {
        $data = Product::query()->where('id', $product)->get();
        if (!$data) {
            return $this->fails('sorry, product not found', 400);
        }
        $transform = new ProductCollection($data);
        return $transform;
    }

    /**
     * update product
     *
     * @param UpdateProductRequest $request
     * @param $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateProductRequest $request, $product)
    {
        $this->admin = JWTAuth::parseToken()->authenticate();
        $validated = $request->validated();
        Product::query()->where('id', $product)->update($validated);
        if($request->hasFile('images')){
            $path = $request->file('images')->store('images/vcanh', 's3');
            $isImage = Image::query()->where('imageable_id', $product)->get();
            if(!$isImage){
                Image::create([
                    'name' => basename($path),
                    'status' => $request->status,
                    'url' => Storage::disk('s3')->url($path),
                    'size' => $request->file('images')->getSize(),
                    'disk' => $request->disk,
                    'imageable_id' => $product,
                    'imageable_type' => Product::class
                ]);
            }else{
                Image::query()->where('imageable_id', $product)->update([
                    'name' => basename($path),
                    'status' => $request->status,
                    'url' => Storage::disk('s3')->url($path),
                    'size' => $request->file('images')->getSize(),
                    'disk' => $request->disk,
                ]);
            }
        }
        $productData = new ProductCollection(Product::query()->where('id', $product)->get());
        return $this->successWithData('product updated successfully', $productData, 200);
    }

    /**
     * delete product
     *
     * @param $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($product)
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        Product::query()->where('id', $product)->delete();
        Image::query()->where('imageable_id', $product)->delete();
        return $this->success('Product deleted successfully', 200);
    }
}
