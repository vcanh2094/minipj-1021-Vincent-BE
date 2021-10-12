<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SlideCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $slides = [];
        foreach ($this->collection as $slide){
            array_push($slides, [
                'name' => $slide->name,
                'status' => ($slide->status) == 1 ? 'active' : 'private',
                'image' => $slide->images()->first(),
            ]);
        }
        return $slides;
    }
}
