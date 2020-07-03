<?php

namespace App\Http\Requests\Backend\System\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
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
            'phone'   => 'required|numeric|regex:/^1[3456789][0-9]{9}$/|unique:admin',
            'username'   => 'required|string|min:6|max:20|unique:admin',
            'nickname'  => 'required|min:2|max:14',
            'password'  => 'required|confirmed|min:6|max:14'
        ];
    }

    public function attributes()
    {
        return [
            'nickname' => '昵称',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['code'=>1,'msg'=>$validator->errors()->first()]));
    }
}
