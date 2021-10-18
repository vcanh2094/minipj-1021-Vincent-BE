<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Transformers\ProductTransformer;
use Flugg\Responder\Http\Responses\SuccessResponseBuilder;
use Flugg\Responder\Responder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\PseudoTypes\LowercaseString;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;


class ProductController extends Controller
{
    protected $user;
    private $products;

    /**
     * show list products
     *
     * @param Request $request
     * @param Responder $responder
     * @return JsonResponse
     */
    public function index(Request $request, Responder $responder): JsonResponse
    {
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
                        ->orWhere('content', 'like', '%'.$request->search.'%')
                        ->orWhere('category_id', 'like', '%'.$request->search.'%')
                        ->orderBy('id');
            })
        ;
        return responder()->success($product_query->paginate(20), new ProductTransformer)->respond();
    }

    /**
     * Store product.
     *
     * @param StoreProductRequest $request
     * @param Image $image
     * @param Responder $responder
     * @return JsonResponse
     */
    public function store(StoreProductRequest $request, Image $image, Responder $responder): JsonResponse
    {
        $this->admin = JWTAuth::parseToken()->authenticate();
        $product = Product::create($request->validated());
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
        return responder()->success(Product::query()->where('id', $product->id)->get(), new ProductTransformer)->respond();
    }

    /**
     * Show product detail.
     *
     * @param $product
     * @param Responder $responder
     * @return SuccessResponseBuilder
     */
    public function show($product, Responder $responder): SuccessResponseBuilder
    {
        return $responder->success(Product::query()->where('id', $product)->get(), new ProductTransformer);
    }

    /**
     * Update product.
     *
     * @param UpdateProductRequest $request
     * @param $product
     * @param Responder $responder
     * @return JsonResponse
     */
    public function update(UpdateProductRequest $request, $product, Responder $responder): JsonResponse
    {
        $this->admin = JWTAuth::parseToken()->authenticate();
        Product::query()->where('id', $product)->update($request->validated());
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
        return responder()->success(Product::query()->where('id', $product)->get(), new ProductTransformer)->respond();
    }

    /**
     * delete product
     *
     * @param $product
     * @param Responder $responder
     * @return JsonResponse
     */
    public function destroy($product, Responder $responder): JsonResponse
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        Product::query()->where('id', $product)->delete();
        Image::query()->where('imageable_id', $product)->delete();
        return $responder->success()->respond();
    }
}
