<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillRequest extends FormRequest
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
            'merchant_id' => 'required|exists:merchant,id',
            'type' => 'required|in:1,2',
            'money' => 'required|numeric|min:0.01',
            'remark' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'type' => '类型',
            'money' => '金额',
            'remark' => '备注'
        ];
    }

}
