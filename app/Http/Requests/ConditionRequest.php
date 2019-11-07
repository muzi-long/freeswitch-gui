<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConditionRequest extends FormRequest
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
            'field'         => 'required|string',
            'expression'    => 'required',
            'break'         => 'required|in:on-false,on-true,always,never',
            'sort'          => 'required|numeric|min:0|max:99',
        ];
    }

    public function attributes()
    {
        return [
            'display_name'  => '名称',
            'field'         => '字段',
            'expression'    => '正则',
            'sort'          => '序号',
        ];
    }

}
