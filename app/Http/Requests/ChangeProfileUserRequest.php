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
            'email' => 'required|string|email|max:100|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'gender' => 'in:1,0',
            'birthday' => 'date_format:Y-m-d',
            'isChangePassword' => 'boolean',
            'old_password' => 'required_if:isChangePassword.*,in:true|string|min:6',
            'new_password' => 'required_if:isChangePassword.*,in:true|min:6'
        ];
    }
}
