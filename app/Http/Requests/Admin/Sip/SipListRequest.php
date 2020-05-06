<?php

namespace App\Http\Requests\Sip;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SipListRequest extends FormRequest
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
            'sip_start' => 'required|numeric|min:1000',
            'sip_end'   => 'required|numeric',
            'password'  => 'required',
            'gateway_id' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'sip_start' => '开始分机',
            'sip_end'   => '结束分机',
            'gateway_id' => '网关'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['code'=>1,'msg'=>$validator->errors()->first()]));
    }

}
