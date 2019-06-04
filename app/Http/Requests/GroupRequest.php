<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
            'name'          => 'required|numeric|min:6000|max:6999|unique:group,name,'.$this->id.',id'
        ];
    }

    public function attributes()
    {
        return [
            'display_name'  => '名称',
            'name'          => '标识'
        ];
    }
}
