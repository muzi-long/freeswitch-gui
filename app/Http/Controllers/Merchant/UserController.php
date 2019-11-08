<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    use AuthenticatesUsers;

    /**
     * 用户登录表单
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return View::make('merchant.user.login');
    }

    /**
     * 验证登录字段
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    //登录成功后的跳转地址
    public function redirectTo()
    {
        return URL::route('merchant.layout');
    }

    /**
     * 退出后的动作
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function loggedOut(Request $request)
    {
        return Redirect::to(URL::route('merchant.user.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('merchant');
    }

    protected function authenticated(Request $request, $user)
    {
        //记录登录成功日志
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
        return View::make('merchant.user.changeMyPassword');
    }

    /**
     * 修改密码
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeMyPassword(ChangePasswordRequest $request)
    {
        $data = $request->all(['old_password','new_password']);
        //验证原密码
        if (!Hash::check($data['old_password'],$request->user('merchant')->getAuthPassword())){
            return Redirect::back()->withErrors('原密码不正确');
        }
        try{
            $request->user('merchant')->fill(['password' => $data['new_password']])->save();
            return Redirect::back()->with(['success'=>'密码修改成功']);
        }catch (\Exception $exception){
            return Redirect::back()->withErrors('修改失败');
        }
    }

}
