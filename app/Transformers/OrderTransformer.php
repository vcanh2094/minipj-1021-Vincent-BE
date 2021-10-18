<?php

namespace App\Transformers;

use App\Models\Order;
use Flugg\Responder\Transformers\Transformer;

class OrderTransformer extends Transformer
{
    /**
     * List of available relations.
     *
     * @var string[]
     */
    protected $relations = [];

    /**
     * List of autoloaded default relations.
     *
     * @var array
     */
    protected $load = [];

    /**
     * @param \App\Transformers\Order $order
     * @return array
     */
    public function transform(Order $order)
    {
        return [
            'id' => $order->id,
            'date_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
//                'user_id' => $order->user_id,
            'total' => $order->total,
//                'payment_method' => $order->payment_method,
            'status' => $order->status,
            'order_details' => $order->order_details()->select('product_name')->get(),
        ];
    }
}
