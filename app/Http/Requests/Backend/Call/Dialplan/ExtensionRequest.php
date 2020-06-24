<?php

namespace App\Http\Requests\Backend\Call\Dialplan;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExtensionRequest extends FormRequest
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
            'name'          => 'required|string|unique:extension,name,'.$this->id.',id',
            'sort'          => 'required|numeric|min:0|max:99',
            'continue'      => 'required|string|in:true,false',
            'context'          => 'required|string|in:default,public'
        ];
    }

    public function attributes()
    {
        return [
            'display_name'  => '名称',
            'name'          => '标识符',
            'sort'          => '序号',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['code'=>1,'msg'=>$validator->errors()->first()]));
    }

}
