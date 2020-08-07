<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\Account\Staff\ChangePasswordRequest;
use App\Http\Requests\Frontend\Account\Staff\ResetPasswordRequest;
use App\Http\Requests\Frontend\Account\Staff\StoreRequest;
use App\Http\Requests\Frontend\Account\Staff\UpdateRequest;
use App\Models\Department;
use App\Models\Merchant;
use App\Models\Role;
use App\Models\Staff;
use App\Models\StaffLoginLog;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
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

    /**
     * 验证通过后执行
     * @param Request $request
     * @param $user
     */
    protected function authenticated(Request $request, $user)
    {
        try {
            //更新登录时间和登录ip
            $user->update([
                'last_login_at' => date('Y-m-d H:i:s'),
                'last_login_ip' => $request->ip(),
            ]);
            $user->load(['merchant','department']);
            //登录日志
            StaffLoginLog::create([
                'merchant_id' => $user->merchant_id,
                'merchant_company_name' => $user->merchant->company_name,
                'department_id' => $user->department_id,
                'department_name' => $user->department->name,
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


    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all(['nickname','username']);
            $res = Staff::with(['sip','department'])
                ->when($data['nickname'],function ($q) use ($data){
                    return $q->where('nickname',$data['nickname']);
                })
                ->when($data['username'],function ($q) use ($data){
                    return $q->where('username',$data['username']);
                })
                ->where('is_merchant',0)
                ->orderBy('id','desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('frontend.account.staff.index');
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $departments = Department::with('childs')
            ->where('parent_id',0)
            ->where('merchant_id',$request->user()->merchant_id)
            ->get();
        return View::make('frontend.account.staff.create',compact('departments'));
    }

    /**
     * 添加
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'nickname',
            'username',
            'password',
            'department_id',
        ]);
        $data['merchant_id'] = $request->user()->merchant_id;
        //验证是否超过商户最大员工数
        $merchant = Merchant::find($data['merchant_id']);
        $count = Staff::where('merchant_id',$data['merchant_id'])->where('is_merchant',0)->count();
        if ($merchant->staff_num - $count < 1){
            return Response::json(['code'=>1,'msg'=>'超出商户最大员工数【'.$merchant->staff_num.'】']);
        }
        $data['password'] = bcrypt($data['password']);
        try {
            Staff::create($data);
            return Response::json(['code' => 0, 'msg' => '添加成功', 'url' => route('frontend.account.staff')]);
        } catch (\Exception $exception) {
            Log::error('添加员工异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '添加失败']);
        }
    }

    /**
     * 更新
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request,$id)
    {
        $staff = Staff::findOrFail($id);
        $departments = Department::with('childs')
            ->where('parent_id',0)
            ->where('merchant_id',$request->user()->merchant_id)
            ->get();
        return View::make('frontend.account.staff.edit',compact('staff','departments'));
    }

    /**
     * 更新
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request,$id)
    {
        $staff = Staff::findOrFail($id);
        $data = $request->all([
            'nickname',
            'username',
            'department_id',
        ]);
        try {
            $staff->update($data);
            return Response::json(['code' => 0, 'msg' => '更新成功', 'url' => route('frontend.account.staff')]);
        } catch (\Exception $exception) {
            Log::error('更新员工异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '更新失败']);
        }
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            Staff::where('merchant_id',$request->user()->merchant_id)->destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除员工异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 分配角色
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function role($id)
    {
        $user = Staff::findOrFail($id);
        $roles = Role::where('guard_name','=',config('freeswitch.frontend_guard'))
            ->whereIn('merchant_id',[$user->merchant_id,0])
            ->get();
        foreach ($roles as $role){
            $role->own = $user->hasRole($role) ? true : false;
        }
        return View::make('frontend.account.staff.role',compact('roles','user'));
    }

    /**
     * 分配角色
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignRole(Request $request,$id)
    {
        $user = Staff::findOrFail($id);
        $roles = $request->get('roles',[]);
        try{
            $user->syncRoles($roles);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('frontend.account.staff')]);
        }catch (\Exception $exception){
            Log::error('为前台员工分配角色异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 重置用户密码表单
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function resetPasswordForm($id)
    {
        $user = Staff::findOrFail($id);
        return View::make('frontend.account.staff.resetPassword',compact('user'));
    }

    /**
     * 重置用户密码
     * @param ResetPasswordRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request,$id)
    {
        $user = Staff::findOrFail($id);
        $data = $request->all(['new_password']);
        try{
            $user->update(['password'=>bcrypt($data['new_password'])]);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('重置前台用户密码异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 更改密码
     * @return \Illuminate\Contracts\View\View
     */
    public function changeMyPasswordForm()
    {
        return View::make('frontend.system.staff.changeMyPassword');
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

    /**
     * 登录日志
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function loginLog(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all(['staff_nickname','staff_username','time_start','time_end']);
            $res = StaffLoginLog::query()
                ->when($data['staff_nickname'],function ($q) use ($data){
                    return $q->where('staff_nickname',$data['staff_nickname']);
                })
                ->when($data['staff_username'],function ($q) use ($data){
                    return $q->where('staff_username',$data['staff_username']);
                })
                ->when($data['time_start']&&!$data['time_end'],function ($q) use ($data){
                    return $q->where('time','>=',$data['time_start']);
                })
                ->when(!$data['time_start']&&$data['time_end'],function ($q) use ($data){
                    return $q->where('time','<=',$data['time_end']);
                })
                ->when($data['time_start']&&$data['time_end'],function ($q) use ($data){
                    return $q->whereBetween('time',[$data['time_start'],$data['time_end']]);
                })
                ->orderBy('id','desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('frontend.system.staff.log');
    }

    /**
     * 个人信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function mine(Request $request)
    {
        $staff = $request->user();
        $staff->load(['department','sip']);
        if ($request->ajax()){
            $nickname = $request->input('nickname');
            try {
                $staff->fill(['nickname' => $nickname])->save();
                return Response::json(['code'=>0,'msg'=>'更新成功']);
            }catch (\Exception $exception){
                Log::error('更新员工信息异常：'.$exception->getMessage());
                return Response::json(['code'=>1,'msg'=>'更新失败']);
            }
        }
        return View::make('frontend.system.staff.mine',compact('staff'));
    }

}
