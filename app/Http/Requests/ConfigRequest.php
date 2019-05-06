<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigRequest extends FormRequest
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
            'label' => 'required',
            'key'   => 'required|string|unique:config,key,'.$this->id.',id',
            'value' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'label' => '标签',
            'key'   => '键',
            'value' => '值',
        ];
    }

}
