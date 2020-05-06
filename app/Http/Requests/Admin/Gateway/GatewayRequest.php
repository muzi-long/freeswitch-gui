<?php

namespace App\Http\Requests\Admin\Gateway;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GatewayRequest extends FormRequest
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
            'name' => 'required',
            'realm' => 'required',
            'username' => 'required',
            'password' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '网关名称',
            'realm' => '地址',
            'username' => '帐号',
            'password' => '密码',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['code'=>1,'msg'=>$validator->errors()->first()]));
    }

}
