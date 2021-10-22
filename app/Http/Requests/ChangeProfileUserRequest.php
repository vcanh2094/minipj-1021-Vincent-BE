<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeProfileUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|between:2,100',
            'email' => "required|email|max:100|unique:users,email,".$this->user()->email.",email",
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'gender' => 'in:1,0',
            'birthday' => 'date_format:Y-m-d',
        ];
    }
}
