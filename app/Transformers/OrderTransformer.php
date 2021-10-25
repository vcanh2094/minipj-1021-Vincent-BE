<?php

namespace App\Transformers;

use App\Models\Order;
use App\Models\OrderDetail;
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
     * @param Order $order
     * @return array
     */
    public function transform(Order $order): array
    {
        return [
            'order_id' => $order->id,
            'date_order' => date('d-m-Y H:i:s', strtotime($order->created_at)),
            'total' => (float) $order->total,
            'status' => $order->status,
            'order_details' => $order->order_details()->select('product_id','product_name', 'product_price', 'product_quantity')->get(),
        ];
    }
}
