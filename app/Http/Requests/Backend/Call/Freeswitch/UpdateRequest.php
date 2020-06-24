<?php

namespace App\Http\Requests\Backend\Call\Freeswitch;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'external_ip' => 'required|ipv4|unique:freeswitch,external_ip,'.$this->id.',id',
            'internal_ip' => 'required|ipv4|unique:freeswitch,internal_ip,'.$this->id.',id',
            'esl_port' => 'required|numeric',
            'esl_password' => 'required',
            'internal_sip_port' => 'required|numeric',
            'swoole_http_port' => 'required|numeric',
            'fs_install_path' => 'required',
            'fs_record_path' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'name' => '名称',
            'external_ip' => '外网ip',
            'internal_ip' => '内网ip',
            'esl_port' => 'esl端口',
            'esl_password' => 'esl密码',
            'internal_sip_port' => '注册端口',
            'swoole_http_port' => 'swoole端口',
            'fs_install_path' => '安装目录',
            'fs_record_path' => '录音目录',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['code'=>1,'msg'=>$validator->errors()->first()]));
    }
}
