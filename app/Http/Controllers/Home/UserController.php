<?php

namespace App\Http\Controllers\Home;

use App\Http\Requests\ChangePasswordRequest;
use App\Models\Merchant;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Jenssegers\Agent\Agent;


class UserController extends Controller
{

    use AuthenticatesUsers;

    /**
     * 用户登录表单
     * @return \Illuminate\Contracts\View\View
     */
    public function showLoginForm()
    {
        return View::make('home.user.login');
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
        return URL::route('home.layout');
    }

    /**
     * 退出后的动作
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function loggedOut(Request $request)
    {
        return Redirect::to(URL::route('home.user.login'));
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

    /**
     * 验证商户、员工信息
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        try{
            $agent = new Agent();
            DB::table('merchant_login_log')->insert([
                'merchant_id' => $user->id,
                'ip' => $request->getClientIp(),
                'platform' => $agent->platform(),
                'browser' => $agent->browser(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }catch (\Exception $exception){
            Log::info('会员ID：'.$user->id.' 登录日志写入失败。'.$exception->getMessage());
        }

        //验证
        if ($user->merchant_id==0){ //商户
            if ($user->status!=1){
                $this->guard()->logout();
                $request->session()->invalidate();
                return Redirect::back()->withErrors('帐号已被禁用');
            }
            $user->load('info');
            if (Carbon::now()->diffInSeconds($user->info->expires_at,false)<0){
                $this->guard()->logout();
                $request->session()->invalidate();
                return Redirect::back()->withErrors('帐号使用已到期，请联系管理员');
            }

        }else { //员工
            if ($user->status!=1){
                $this->guard()->logout();
                $request->session()->invalidate();
                return Redirect::back()->withErrors('帐号已被禁用');
            }
            //验证员工的商户
            $merchant = Merchant::with('info')->where('merchant_id',0)->where('id',$user->merchant_id)->first();
            if ($merchant==null){
                $this->guard()->logout();
                $request->session()->invalidate();
                return Redirect::back()->withErrors('商户帐号不存在');
            }
            if ($merchant->status!=1){
                $this->guard()->logout();
                $request->session()->invalidate();
                return Redirect::back()->withErrors('商户帐号已被禁用');
            }
            if (Carbon::now()->diffInSeconds($merchant->info->expires_at,false)<0){
                $this->guard()->logout();
                $request->session()->invalidate();
                return Redirect::back()->withErrors('商户帐号使用已到期，请联系管理员');
            }
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
        return View::make('home.user.changeMyPassword');
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
