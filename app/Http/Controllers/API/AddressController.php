<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Transformers\AddressTransformer;
use Illuminate\Http\JsonResponse;

class AddressController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(){
        $address = Address::query()->where('user_id', auth()->user()->id)->get();
        return responder()->success($address, new AddressTransformer)->respond();
    }

    /**
     * @param StoreAddressRequest $request
     * @return JsonResponse
     */
    public function store(StoreAddressRequest $request){
        $address = Address::create(array_merge($request->validated(),['user_id' => auth()->user()->id]));
        return responder()->success($address, new AddressTransformer)->respond();
    }

    /**
     * @param UpdateAddressRequest $request
     * @return JsonResponse
     */
    public function update(UpdateAddressRequest $request){
        $query = Address::query()->where('user_id', auth()->user()->id);
        $isAddress = $query->value('user_id');
        if($isAddress == null ){
            Address::create(
                array_merge(
                    ['user_id' => auth()->user()->id],
                    $request->validated()
                )
            );
        }else{
            Address::where('user_id', auth()->user()->id)->update(
                array_merge(
                    ['user_id' => auth()->user()->id],
                    $request->validated(),
                )
            );
        }
        return responder()->success($query, new AddressTransformer)->respond();
    }
}
