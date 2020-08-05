<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\StaffLoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class StaffController extends Controller
{

    use AuthenticatesUsers;

    /**
     * 登录成功后的跳转地址
     * @var string
     */
    public $redirectTo = '/frontend';

    /**
     * 用户登录表单
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return View::make('frontend.system.staff.login');
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
        Session::forget('frontend_menus');
        return Redirect::route('frontend.system.staff.login');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('frontend');
    }

    protected function authenticated(Request $request, $user)
    {
        try {
            //更新登录时间和登录ip
            $user->update([
                'last_login_at' => date('Y-m-d H:i:s'),
                'last_login_ip' => $request->ip(),
            ]);
            //登录日志
            StaffLoginLog::create([
                'merchant_id' => $user->merchant_id,
                'merchant_company_name' => $user->merchant()->company_name,
                'department_id' => $user->department_id,
                'department_name' => $user->department()->name,
                'staff_id' => $user->id,
                'staff_nickname' => $user->nickname,
                'staff_username' => $user->username,
                'ip' => $request->ip(),
                'time' => date('Y-m-d H:i:s'),
            ]);
            //缓存后台菜单
            $menus = $user->menus();
            Session::put('frontend_menus',$menus);
        }catch (\Exception $exception){
            Log::error('前台登录异常：'.$exception->getMessage());
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




}
