<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * 登录表单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('admin.login_register.login');
    }

    /**
     * 验证登录字段
     * @param Request $request
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'captcha' => 'required|captcha',
            $this->username() => 'required|string',
            'password' => 'required|string',
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
     * 登录成功后的跳转地址
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectTo()
    {
        return route('admin.layout');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return redirect(route('admin.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

}
