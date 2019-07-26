<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'merchant_id' => 'required|exists:merchant,id'
        ];
    }

    public function attributes()
    {
        return [
            'sip_start' => '开始分机',
            'sip_end'   => '结束分机',
            'merchant_id' => '商户'
        ];
    }
}
