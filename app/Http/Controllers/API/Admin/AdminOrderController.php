<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Transformers\OrderTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(){
        $orders = Order::all();
        return responder()->success($orders, new OrderTransformer)->respond();
    }

    public function update($order, Request $request){
        Order::findOrFail($order)
            ->update(array('status' => $request->status));
        return responder()->success()->respond();
    }
}
