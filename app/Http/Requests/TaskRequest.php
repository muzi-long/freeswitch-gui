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
            'name'              => 'required',
            'datetime_start'    => 'required|date_format:Y-m-d H\:i\:s|before:datetime_end',
            'datetime_end'      => 'required|date_format:Y-m-d H\:i\:s|after:datetime_start',
            'gateway_id'           => 'required|exists:gateway,id',
            'queue_id'             => 'required|exists:queue,id',
            'max_channel'       => 'required|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'name'              => '名称',
            'datetime_start'    => '开始时间',
            'datetime_end'      => '结束时间',
            'gateway_id'           => '网关',
            'queue_id'             => '队列',
            'max_channel'       => '最大并发',
        ];
    }

}
