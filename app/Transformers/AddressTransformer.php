<?php

namespace App\Transformers;


use App\Models\Address;
use Flugg\Responder\Transformers\Transformer;

class AddressTransformer extends Transformer
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
     * @param Address $address
     * @return array
     */
    public function transform(Address $address)
    {
        return [
            'id' => (int) $address->id,
            'province' => $address->province,
            'district' => $address->district,
            'ward' => $address->ward,
            'street_name' => $address->street_name,
        ];
    }
}
