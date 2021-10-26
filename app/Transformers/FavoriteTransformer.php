<?php

namespace App\Transformers;

use App\Models\Favorite;
use Flugg\Responder\Transformers\Transformer;

class FavoriteTransformer extends Transformer
{
    /**
     * List of available relations.
     *
     * @var string[]
     */
    protected $relations = [
        'product' => ProductTransformer::class
    ];

    /**
     * List of autoloaded default relations.
     *
     * @var array
     */
    protected $load = [];

    /**
     * Transform the model.
     *
     * @param Favorite $favorite
     * @return array
     */
    public function transform(Favorite $favorite)
    {
        return [
            'id' => (int) $favorite->id,
            'user_id' => $favorite->user_id,
        ];
    }
}
