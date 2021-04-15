<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Platform\Staff\StoreRequest;
use App\Http\Requests\Backend\Platform\Staff\UpdateRequest;
use App\Http\Requests\Frontend\Account\Staff\ResetPasswordRequest;
use App\Models\Freeswitch;
use App\Models\Merchant;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all(['merchant_id','nickname','username']);
            $res = Staff::with(['merchant','sip','department'])
            ->when($data['merchant_id'],function ($q) use ($data){
                return $q->where('merchant_id',$data['merchant_id']);
            })
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
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.platform.staff.index',compact('merchants'));
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $merchants = Merchant::orderBy('id', 'desc')->get();
        return View::make('backend.platform.staff.create', compact('merchants'));
    }

    /**
     * 添加
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'merchant_id',
            'nickname',
            'username',
            'password',
        ]);
        //验证是否超过商户最大员工数
        $merchant = Merchant::find($data['merchant_id']);
        $count = Staff::where('merchant_id',$data['merchant_id'])->where('is_merchant',0)->count();
        if ($merchant->staff_num - $count < 1){
            return Response::json(['code'=>1,'msg'=>'超出商户最大员工数【'.$merchant->staff_num.'】']);
        }
        $data['merchant_id'] = $data['merchant_id']==null?0:$data['merchant_id'];
        $data['password'] = bcrypt($data['password']);
        try {
            Staff::create($data);
            return Response::json(['code' => 0, 'msg' => '添加成功', 'url' => route('backend.platform.staff')]);
        } catch (\Exception $exception) {
            Log::error('添加员工异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '添加失败']);
        }
    }

    /**
     * 更新
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $merchants = Merchant::orderBy('id', 'desc')->get();
        $staff = Staff::findOrFail($id);
        return View::make('backend.platform.staff.edit',compact('staff','merchants'));
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
            'merchant_id',
            'nickname',
            'username',
        ]);
        try {
            $data['merchant_id'] = $data['merchant_id']==null?0:$data['merchant_id'];
            $staff->update($data);
            return Response::json(['code' => 0, 'msg' => '更新成功', 'url' => route('backend.platform.staff')]);
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
            Staff::destroy($ids);
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
        return View::make('backend.platform.staff.role',compact('roles','user'));
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
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.platform.staff')]);
        }catch (\Exception $exception){
            Log::error('为后台员工分配角色异常：'.$exception->getMessage());
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
        return View::make('backend.platform.staff.resetPassword',compact('user'));
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

}
