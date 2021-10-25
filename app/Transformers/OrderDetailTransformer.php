<?php

namespace App\Transformers;

use App\Models\OrderDetail;
use Flugg\Responder\Transformers\Transformer;

class OrderDetailTransformer extends Transformer
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
     * Transform the model.
     *
     * @param OrderDetail $orderDetail
     * @return array
     */
    public function transform(OrderDetail $orderDetail): array
    {
        return [
            'order' => $orderDetail->order()->select('id', 'status','total', 'created_at AS date_order', 'payment_method')->get(),
            'user' => $orderDetail->order()->where('order_id', $orderDetail->order()->value('id'))->get(),
            'product' => $orderDetail->product()->select('id', 'name', 'price')->get(),
            'product_quantity' => (int) $orderDetail->product_quantity,
        ];
    }
}
