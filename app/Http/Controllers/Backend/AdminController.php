<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\ChangePasswordRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class AdminController extends Controller
{
    use AuthenticatesUsers;

    /**
     * 登录成功后的跳转地址
     * @var string
     */
    public $redirectTo = '/backend';

    /**
     * 用户登录表单
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return View::make('backend.admin.login');
    }

    /**
     * 验证登录字段
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            //'captcha' => 'required|captcha',
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * 退出后的动作
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function loggedOut()
    {
        return Redirect::route('backend.admin.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('backend');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $request->ip(),
        ]);
    }

    /**
     * 用于登录的字段
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 更改密码
     * @return \Illuminate\Contracts\View\View
     */
    public function changeMyPasswordForm()
    {
        return View::make('backend.admin.changeMyPassword');
    }

    /**
     * 修改自己的密码
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeMyPassword(ChangePasswordRequest $request)
    {
        $data = $request->all(['old_password','new_password']);
        //验证原密码
        if (!Hash::check($data['old_password'],$request->user()->getAuthPassword())){
            return Response::json(['code'=>1,'msg'=>'原密码不正确']);
        }
        try{
            $request->user()->fill(['password' => bcrypt($data['new_password'])])->save();
            return Response::json(['code'=>0,'msg'=>'密码修改成功']);
        }catch (\Exception $exception){
            Log::error('修改密码异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'修改失败']);
        }
    }

}
