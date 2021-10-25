<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Transformers\OrderTransformer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{

    /**
     * Show list orders of a user
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $query = Order::query();
        return responder()->success($query->paginate(20), new OrderTransformer)->respond();
    }

    /**
     * Show detail of an order
     *
     * @param $order
     * @return JsonResponse
     */
    public function show($order): JsonResponse
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $query = Order::query()->where('orders.id', $order)
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('images', 'products.id', '=', 'imageable_id')
            ->select([
                'orders.id AS order_id',
                'orders.status AS order_status',
                'orders.created_at AS date_order',
                'orders.total',
                'images.url AS image_url',
                'images.imageable_type AS image_type',
                'order_details.product_name',
                'order_details.product_price',
                'order_details.product_quantity'
            ])
            ->get();
        return responder()->success($query)->respond();

//        return responder()->success(Order::where('id', $order), OrderTransformer::class)
//                ->with([
//                    'order_details' => function($query){
//                        $query->select('product_name','product_price', 'product_quantity');
//                    },
//                    'order_details.products.images' =>function($query){
//                        $query->select('url', 'imageable_type');
//                    }
//                ])
//                ->respond();
    }

    /**
     * store new order
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $this->user = JWTAuth::parseToken()->authenticate();
        $order = Order::create(array_merge($request->validated(),['user_id' => $request->user()->id]));
        foreach( $request->products as $product => $value){
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $value['id'],
                'product_name' => $value['name'],
                'product_price' => $value['price'],
                'product_quantity' => $value['quantity'],
            ]);
        }
        return responder()->success($order, OrderTransformer::class)->respond();
    }
}
