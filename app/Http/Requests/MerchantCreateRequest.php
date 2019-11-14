<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantCreateRequest extends FormRequest
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
            'username' => 'required|string|min:4|max:14|unique:merchant,username,'.$this->id.',id',
            'password' => 'required|string|min:6|max:14',
            'status' => 'required|numeric|in:'.implode(',',array_keys(config('freeswitch.merchant_status'))),
            'contact_name' => 'required',
            'contact_phone' => 'required',
        ];
    }
}
