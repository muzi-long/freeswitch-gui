<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
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
        $rule = [
            'username' => 'required|unique:member,username,'.$this->id.',id',
            'contact_phone' => 'required|numeric|regex:/^1[34578][0-9]{9}$/|unique:member,contact_phone,'.$this->id.',id',
            'contact_name' => 'required',
        ];
        if ($this->sip_id){
            $rule['sip_id'] = 'required|unique:member,sip_id,'.$this->id.',id';
        }
        if ($this->id){
            if ($this->get('password') || $this->get('password_confirmation')){
                $rule['password'] = 'required|confirmed|min:6|max:14';
            }
        }else{
            $rule['password'] = 'required|confirmed|min:6|max:14';
        }

        return $rule;
    }

    public function attributes()
    {
        return [
            'username' => '帐号',
            'contact_phone' => '联系电话',
            'contact_name' => '联系人',
            'sip_id' => '分机号',
        ];
    }

    public function messages()
    {
        return [
            'sip_id.unique' => ':attribute 已被使用',
        ];
    }

}
