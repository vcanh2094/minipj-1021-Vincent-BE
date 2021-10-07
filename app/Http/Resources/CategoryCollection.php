<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function toArray($request)
    {

        // final array to be return.
        $categories = [];

        foreach($this->collection as $category) {

            array_push($categories, [
                'name' => $category->name,
                'status' => (($category->status == 1) ? ('active') : ('private')),
            ]);

        }

        return $categories;
    }
}
