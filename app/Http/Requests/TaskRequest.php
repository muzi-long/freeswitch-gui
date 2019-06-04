<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
            'name'          => 'required',
            'date_start'    => 'required|date_format:Y-m-d|before_or_equal:date_end',
            'date_end'      => 'required|date_format:Y-m-d|after_or_equal:date_start',
            'time_start'    => 'required|date_format:H\:i\:s|before:time_end',
            'time_end'      => 'required|date_format:H\:i\:s|after:time_start',
            'gateway_id'    => 'required|exists:gateway,id',
            'queue_id'      => 'required|exists:queue,id',
            'max_channel'   => 'required|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'name'          => '名称',
            'date_start'    => '开始日期',
            'date_end'      => '结束日期',
            'time_start'    => '开始时间',
            'time_end'      => '结束时间',
            'gateway_id'    => '网关',
            'queue_id'      => '队列',
            'max_channel'   => '最大并发',
        ];
    }

}
