<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Traits\RespondsWithHttpStatus;
use App\Transformers\OrderTransformer;
use Flugg\Responder\Responder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrderController extends Controller
{
    use RespondsWithHttpStatus;

    /**
     * Show list orders of a user
     *
     * @return JsonResponse
     */
    public function index(Responder $responder){
        $this->user = JWTAuth::parseToken()->authenticate();
        $query = Order::query()->with('order_details');
        return responder()->success($query->paginate(20), new OrderTransformer)->respond();

    }

    /**
     * show detail of an order
     *
     * @param $order
     * @return JsonResponse
     */
    public function show($order){
        $this->user = JWTAuth::parseToken()->authenticate();
        $result = DB::table('orders')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->join('products', 'order_details.product_id', '=', 'products.id')
            ->join('images', 'products.id', '=', 'imageable_id')
            ->where('orders.id', $order)
            ->select(
                'orders.id AS order_id',
                'orders.status AS order_status',
                'orders.created_at AS date_order',
                'orders.total',
                'images.name AS image_name',
                'images.url AS image_url',
                'images.imageable_type AS image_type',
                'order_details.product_name',
                'order_details.product_price',
                'order_details.product_quantity'
            )
            ->get();
        return $this->successWithData('order detail fetched successfully', $result, 200);
    }

    /**
     * store new order
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request){
        $this->user = JWTAuth::parseToken()->authenticate();
        $order = Order::create([
            'user_id' => $request->user()->id,
            'total' => $request->total,
            'payment_method' => $request->payment_method,
            'status' => $request->status
        ]);
        foreach( $request->products as $product => $value){
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $value['id'],
                'product_name' => $value['name'],
                'product_price' => $value['price'],
                'product_quantity' => $value['quantity'],
            ]);
        }
        return $this->success('Order created successfully', 200);
    }
}
