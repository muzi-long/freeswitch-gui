<?php

namespace App\Http\Controllers\Home;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\Configuration;
use App\Models\LoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;


class UserController extends Controller
{

    use AuthenticatesUsers;

    /**
     * 用户登录表单
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return View::make('admin.user.login');
    }

    /**
     * 验证登录字段
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'captcha' => 'required|captcha',
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    //登录成功后的跳转地址
    public function redirectTo()
    {
        return URL::route('admin.layout');
    }

    /**
     * 退出后的动作
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function loggedOut(Request $request)
    {
        return Redirect::to(URL::route('admin.user.login'));
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('member');
    }

    protected function authenticated(Request $request, $user)
    {
        //缓存配置信息
        $configuration = Configuration::pluck('val','key');
        $request->session()->put('configuration',$configuration);
        //记录登录成功日志
        if ($configuration['login_log']==1){
            LoginLog::create([
                'username' => $user->username,
                'ip' => $request->ip(),
                'method' => $request->method(),
                'user_agent' => $request->header('User-Agent'),
                'remark' => '登录成功',
            ]);
        }
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
        return View::make('admin.user.changeMyPassword');
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
        if (!Hash::check($data['old_password'],$request->user()->getAuthPassword())){
            return Redirect::back()->withErrors('原密码不正确');
        }
        try{
            $request->user()->fill(['password' => $data['new_password']])->save();
            return Redirect::back()->with(['success'=>'密码修改成功']);
        }catch (\Exception $exception){
            return Redirect::back()->withErrors('修改失败');
        }
    }

}
