<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantUpdateRequest extends FormRequest
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
        $reg = [
            'username' => 'required|string|min:4|max:14|unique:merchant,username,'.$this->id.',id',
            'status' => 'required|numeric|in:'.implode(',',array_keys(config('freeswitch.merchant_status'))),
            'company_name' => 'required',
            'sip_num' => 'required|numeric|min:0',
            'expires_at' => 'required|date_format:Y-m-d H\:i\:s'
        ];
        if ($this->get('password')){
            $reg['password'] = 'required|string|min:6|max:14';
        }
        return $reg;
    }
}
