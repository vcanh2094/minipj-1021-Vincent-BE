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
    protected $relations = [
        'order_details' => OrderDetailTransformer::class,
        'address' => AddressTransformer::class,
        'user' => UserTransformer::class,
    ];

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
            'id' => $order->id,
            'date_order' => strtotime($order->created_at),
            'total' => (float) $order->total,
            'status' => $order->status,
        ];
    }
}
