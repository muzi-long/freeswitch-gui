<?php

namespace App\Http\Controllers;

use App\Events\UserLogined;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = "/";

    /**
     * 用户登录表单
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return View::make('auth.login');
    }

    /**
     * 追加验证状态为启用的条件
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'),['status' => 1]);
    }

    /**
     * 验证失败返回信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return $this->error("帐号密码错误");
    }

    protected function authenticated(Request $request, $user)
    {
        event(new UserLogined($user));
        return $this->success("登录成功");
    }

    /**
     * 用于登录的字段
     * @return string
     */
    public function username()
    {
        return 'name';
    }

    protected function loggedOut(Request $request)
    {
        return Redirect::route('auth.login');
    }

}
