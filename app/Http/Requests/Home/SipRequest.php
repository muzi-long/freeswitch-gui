<?php

namespace App\Http\Requests\Home;

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
            'username'  => 'required|numeric|min:1000|max:9999|unique:sip,username,'.$this->id.',id',
            'password'  => 'required',
            'gateway_id' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'username' => '分机号',
            'gateway_id' => '网关'
        ];
    }

}
