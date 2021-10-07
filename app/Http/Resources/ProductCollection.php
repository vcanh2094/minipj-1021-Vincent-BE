<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $products = [];
        foreach ($this->collection as $product){
            array_push($products, [
                'name' => $product->name,
                'price' => $product->price,
                'content' => $product->content,
                'category_id' => $product->category_id,
                'feature' => ($product->feature) == 1 ? ('Yes') : ('No'),
                'discount' => ($product->sale) <> 0 ? (($product->sale*100).'%') : ('No'),
            ]);
        }
        return $products;
    }
}