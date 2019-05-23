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
            'name'                  => 'required|string|min:2|unique:agents,name,'.$this->id.',id',
            'contact'               => 'required',
            'status'                => 'required|string|in:Logged Out,Available,Available (On Demand),On Break',
            'state'                 => 'required|string|in:Idle,Waiting,In a queue call',
            'max_no_answer'         => 'required|numeric|min:0',
            'wrap_up_time'          => 'required|numeric|min:0',
            'reject_delay_time'     => 'required|numeric|min:0',
            'busy_delay_time'       => 'required|numeric|min:0',
            'no_answer_delay_time'  => 'required|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'name'                  => '坐席名称',
            'contact'               => '分机号',
            'status'                => '坐席状态',
            'state'                 => '呼叫状态',
            'max_no_answer'         => '最大无应答次数',
            'wrap_up_time'          => '通话间隔',
            'reject_delay_time'     => '拒接间隔时间',
            'busy_delay_time'       => '忙重试间隔时间',
            'no_answer_delay_time'  => '无应答重试间隔',
        ];
    }

}
