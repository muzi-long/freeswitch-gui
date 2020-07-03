<?php

namespace App\Http\Requests\Backend\Platform\Merchant;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRequest extends FormRequest
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
            //帐号信息
            'username' => 'required|string|min:6|max:16|unique:staff',
            'nickname'  => 'required|min:2|max:14',
            'password'  => 'required|confirmed|min:6|max:14',
            //商户信息
            'company_name'  => 'required',
            'contact_name'  => 'required',
            'contact_phone'  => 'required',
            'staff_num'  => 'required|numeric|min:0',
            'sip_num'  => 'required|numeric|min:0',
            'gateway_num'  => 'required|numeric|min:0',
            'agent_num'  => 'required|numeric|min:0',
            'queue_num'  => 'required|numeric|min:0',
            'task_num'  => 'required|numeric|min:0',
            'expire_at'  => 'required',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['code'=>1,'msg'=>$validator->errors()->first()]));
    }
}
