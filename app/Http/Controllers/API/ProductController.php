<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Favorite;
use App\Models\Image;
use App\Models\Product;
use App\Services\ProductService;
use App\Transformers\ProductTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;


class ProductController extends Controller
{
    protected $user;

    /**
     * show list products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $productQuery = Product::query()
            ->when($request->has('category'), function($query) use ($request){
                return $query->where('category_id', $request->category);
            })
            ->when($request->has('feature'), function ($query){
                return $query->where('feature', 1)
                    ->orderByDesc('id');
            })
            ->when($request->has('sale'), function ($query){
                return $query->where('sale', '>', 0)
                    ->orderByDesc('id');
            })
            ->when($request->has('search'), function ($query) use ($request){
                return $query->where('name', 'like','%'.$request->search.'%')
                    ->orWhere('content', 'like', '%'.$request->search.'%')
                    ->orWhere('category_id', 'like', '%'.$request->search.'%')
                    ->orWhere('price', 'like', '%'.$request->search.'%')
                    ->orderBy('id');
            })
            ->when($request->has('asc'), function($query){
                return $query->orderBy('price');
            })
            ->when($request->has('desc'), function($query){
                return $query->orderByDesc('price');
            })
            ->when($request->has('sort-by-sale'), function($query){
                return $query->orderByDesc('sale');
            })
            ->when($request->has('date-update'), function($query){
                return $query->orderByDesc('updated_at');
            })
        ;
        return responder()->success($productQuery->paginate($request->perPage), new ProductTransformer)->with(['category', 'images'])->respond();
    }

    /**
     * Store product.
     *
     * @param StoreProductRequest $request
     * @param ProductService $productService
     * @return JsonResponse
     */
    public function store( StoreProductRequest $request, ProductService $productService)
    {
        $product = Product::create($request->validated());
        $productService->handleUploadProductImage($request->images,$product->id);
        return responder()->success($product, new ProductTransformer)->respond();
    }

    /**
     * Update product.
     *
     * @param UpdateProductRequest $request
     * @param $product
     * @param ProductService $productService
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, $product, ProductService $productService)
    {
        Product::query()->where('id', $product)->update($request->validated());
        if($request->hasFile('images')){
            $path = $request->file('images')->store('images/vcanh', 's3');
            $isImage = Image::where('imageable_id', $product)->first();
            if(!$isImage){
                $productService->handleUpdateProductImage($request->file('images'), $path, $product);
            }else{
                Image::query()->where('imageable_id', $product)->update([
                    'name' => basename($path),
                    'url' => Storage::disk('s3')->url($path),
                    'size' => $request->file('images')->getSize(),
                ]);
            }
        }
        return responder()->success(Product::query()->where('id', $product)->get(), new ProductTransformer)->respond();
    }

    /**
     * Delete product
     *
     * @param $product
     * @return JsonResponse
     */
    public function destroy($product)
    {
        Product::query()->where('id', $product)->delete();
        Image::query()->where('imageable_id', $product)->delete();
        Favorite::query()->where('product_id', $product)->delete();
        return responder()->success()->respond();
    }
}
