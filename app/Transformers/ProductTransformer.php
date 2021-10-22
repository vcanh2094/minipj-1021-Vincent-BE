<?php

namespace App\Transformers;

use App\Models\Product;
use Flugg\Responder\Transformers\Transformer;

class ProductTransformer extends Transformer
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
     * @param  \App\Product $product
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'id' => (int) $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'content' => $product->content,
            'description' => $product->description,
            'category_id' => $product->category_id,
            'feature' => ($product->feature) == 1 ? ('Yes') : ('No'),
            'discount' => ($product->sale) <> 0 ? (($product->sale*100).'%') : ('No'),
            'images' => $product->images()->select(['id', 'name', 'url'])->get(),
            'date_update' => date('d-m-Y H:i', strtotime($product->updated_at)),
        ];
    }
}
