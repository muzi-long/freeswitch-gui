<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QueueRequest extends FormRequest
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
            'display_name'                  => 'required',
            'strategy'                      => 'required',
            'max_wait_time'                 => 'required|numeric|min:0',
        ];
    }

    public function attributes()
    {
        return [
            'display_name'                  => '队列名称',
            'strategy'                      => '振铃策略',
            'max_wait_time'                 => '超时时间',
        ];
    }

}
