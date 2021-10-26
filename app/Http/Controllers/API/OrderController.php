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
    public function index()
    {
        JWTAuth::parseToken()->authenticate();
        $query = Order::query();
        return responder()->success($query->paginate(20), new OrderTransformer)->respond();
    }

    /**
     * Show detail of an order
     *
     * @param $order
     * @return JsonResponse
     */
    public function show($order)
    {
        JWTAuth::parseToken()->authenticate();
        return responder()->success(Order::query()->where('id', $order), new OrderTransformer)->respond();
    }

    /**
     * store new order
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        JWTAuth::parseToken()->authenticate();
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
