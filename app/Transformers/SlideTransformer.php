<?php

namespace App\Transformers;

use App\Models\Slide;
use Flugg\Responder\Transformers\Transformer;

class SlideTransformer extends Transformer
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
     * @param Slide $slide
     * @return array
     */
    public function transform(Slide $slide)
    {
        return [
            'id' => (int) $slide->id,
            'name' => (string) $slide->name,
            'status' => ($slide->status) == 1 ? 'active' : 'private',
            'image' => $slide->images()->select(['id', 'name', 'url', 'status', 'imageable_type AS image_type'])->first(),
        ];
    }
}
