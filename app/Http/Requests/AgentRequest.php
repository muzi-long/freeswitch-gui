<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgentRequest extends FormRequest
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
            'display_name'          => 'required',
            'name'                  => 'required|numeric|min:7000|max:7999|unique:queue,name,'.$this->id.',id',
            'originate_type'        => 'required',
            'originate_number'      => 'required',
            'status'                => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'display_name'          => '坐席名称',
            'name'                  => '坐席号码',
            'originate_type'        => '呼叫类型',
            'originate_number'      => '呼叫号码',
            'status'                => '状态'
        ];
    }

}
