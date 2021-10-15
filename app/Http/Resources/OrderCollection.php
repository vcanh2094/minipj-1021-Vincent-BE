<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $orders =[];
        foreach ($this->collection as $order){
            array_push($orders, [
                'id' => $order->id,
                'date_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
//                'user_id' => $order->user_id,
                'total' => $order->total,
//                'payment_method' => $order->payment_method,
                'status' => $order->status,

            ]);
        }
        return $orders;
    }
}
