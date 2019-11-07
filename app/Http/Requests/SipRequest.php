<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SipRequest extends FormRequest
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
            'username'  => 'required|numeric|min:1000|unique:sip,username,'.$this->id.',id',
            'password'  => 'required',
            'merchant_gateway' => 'required|regex:/\d,\d/'
        ];
    }

    public function attributes()
    {
        return [
            'username' => '分机号',
            'merchant_gateway' => '网关'
        ];
    }

    public function messages()
    {
        return [
            'merchant_gateway.regex'=>'请选择网关',
        ];
    }


}
