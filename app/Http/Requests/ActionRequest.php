<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActionRequest extends FormRequest
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
            'display_name'  => 'required',
            'application'   => 'required',
            'sort'          => 'required|numeric|min:0|max:99',
        ];
    }

    public function attributes()
    {
        return [
            'display_name'  => '名称',
            'application'   => '应用',
            'sort'          => '序号',
        ];
    }

}
