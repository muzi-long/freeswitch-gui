<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IvrRequest extends FormRequest
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
            'display_name' => 'required',
            'name' => 'required|unique:ivr,name,'.$this->id.',id',
            'greet_long' => 'required',
            'greet_short' => 'required',
            //'invalid_sound' => 'required',
            //'exit_sound' => 'required',
            //'confirm_macro',
            //'confirm_key',
            //'tts_engine',
            //'tts_voice',
            //'confirm_attempts',
            'timeout' => 'required|numeric|min:0',
            'inter_digit_timeout' => 'required|numeric',
            'max_failures' => 'required|numeric',
            'max_timeouts' => 'required|numeric',
            'digit_len' => 'required|numeric|min:1',
        ];
    }
}
