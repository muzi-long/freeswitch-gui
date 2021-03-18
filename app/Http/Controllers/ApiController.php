<?php

namespace App\Http\Controllers;

use App\Models\Cdr;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ApiController extends Controller
{
    public function getPermissionByRoleId(Request $request)
    {
        $role_id = $request->input('role_id');
        $role = null;
        $checkedIds = [];
        if ($role_id){
            $role = Role::query()->where('id',$role_id)->first();
        }
        $permissions = Permission::query()->orderByDesc('id')->get();
        foreach ($permissions as $permission){
            if ($role != null){
                if ($role->hasPermissionTo($permission)){
                    array_push($checkedIds,$permission->id);
                }
            }
        }
        return $this->success('ok',['trees' => $permissions,'checkedId'=>$checkedIds]);
    }

    public function getRoleByUserId(Request $request)
    {
        $data = [];
        $user_id = $request->input('user_id');
        $user = null;
        if ($user_id){
            $user = User::query()->where('id',$user_id)->first();
        }
        $roles = Role::query()->orderByDesc('id')->get();
        foreach ($roles as $role){
            array_push($data,[
                'name' => $role->display_name,
                'value' => $role->id,
                'selected' => $user != null && $user->hasRole($role),
            ]);
        }
        return $this->success('ok',$data);
    }

    /**
     * 呼叫接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function call(Request $request)
    {

        $user_id = $request->input('user_id');
        $callee = $request->input('callee');
        $user_data = $request->input('user_data');
        $user = User::query()->with('sip')->where('id','=',$user_id)->first();
        if ($user->sip == null){
            return $this->error('用户未分配外呼号');
        }
        if ($user->sip->status != 1){
            return $this->error('用户外呼号未在线');
        }
        try {
            $cdr = Cdr::create([
                'uuid' => uuid_generate(),
                'aleg_uuid' => uuid_generate(),
                'bleg_uuid' => uuid_generate(),
                'caller' => $user->sip->username,
                'callee' => $callee,
                'department_id' => $user->department_id,
                'user_id' => $user->id,
                'user_nickname' => $user->nickname,
                'sip_id' => $user->sip->id,
                'user_data' => $user_data,
            ]);
            Redis::rpush(config('freeswitch.redis_key.dial'),$cdr->uuid);
            return $this->success('呼叫成功',[
                'uuid' => $cdr->uuid,
                'call_time' => date('Y-m-d H:i:s'),
            ]);
        }catch (\Exception $exception){
            Log::error('呼叫异常：'.$exception->getMessage());
            return $this->error('呼叫失败');
        }
    }

}
