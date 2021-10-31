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
    public function index(Request $request)
    {
        $query = Order::query()->where('user_id', auth()->user()->id)->orderByDesc('created_at');
        return responder()->success($query->paginate($request->perPage), new OrderTransformer)->respond();
    }

    /**
     * store new order
     *
     * @param StoreOrderRequest $request
     * @return JsonResponse
     */
    public function store(StoreOrderRequest $request)
    {
        $order = Order::create(array_merge($request->validated(),['user_id' => auth()->user()->id]));
        foreach ($request->products as $product){
            $orderDetails[] = [
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'product_price' => $product['price'],
                'product_quantity' => $product['quantity'],
            ];
        }
        $order->orderDetails()->createMany($orderDetails);
        return responder()->success($order, OrderTransformer::class)->respond();
    }
}
