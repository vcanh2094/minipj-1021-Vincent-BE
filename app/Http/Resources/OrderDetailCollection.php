<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderDetailCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $order_details =[];
        foreach ($this->collection as $order_detail){
            array_push($order_details, [
//                'id' => $order_detail->id,
//                'user_id' => $order_detail->user_id,
//                'product_id' => $order_detail->product_id,2
                'product_name' => $order_detail->product_name,
//                'product_price' => $order_detail->product_price,
//                'product_quantity' => $order_detail->product_quantity,
            ]);
        }
        return $order_details;
    }
}
