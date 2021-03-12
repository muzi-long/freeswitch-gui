<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

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

}
