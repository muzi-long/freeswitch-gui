<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Sip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class UserController extends Controller
{
    /**
     * 用户列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = User::query()
                ->with(['department','sip'])
                ->orderByDesc('id')
                ->orderByDesc('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('system.user.index');
    }

    /**
     * 添加用户
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $sips = Sip::query()->get();
        $exsits = User::query()->where('sip_id','>',0)->pluck('sip_id')->toArray();
        return View::make('system.user.create',compact('sips','exsits'));
    }

    /**
     * 添加用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all(['phone','name','password','nickname','role_ids','sip_id','department_id']);
        $data['role_ids'] = $data['role_ids'] == null ? [] : explode(',',$data['role_ids']);
        $count = User::query()->where('name','=',$data['name'])->count();
        if ($count){
            return $this->error('帐号已存在');
        }
        try{
            $user = User::create($data);
            $user->syncRoles($data['role_ids']);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加用户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 更新用户
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $sips = Sip::query()->get();
        $exsits = User::query()->where('id','!=',$id)->where('sip_id','>',0)->pluck('sip_id')->toArray();
        return View::make('system.user.edit',compact('user','sips', 'exsits'));
    }

    /**
     * 更新用户
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->all(['name','phone','nickname','password','role_ids','sip_id','department_id']);
        $data['role_ids'] = $data['role_ids'] == null ? [] : explode(',',$data['role_ids']);
        if ($data['password']){
            $data['password'] = bcrypt($data['password']);
        }else{
            unset($data['password']);
        }
        try{
            $user->update($data);
            $user->syncRoles($data['role_ids']);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新用户信息异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 删除用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        if (!is_array($ids) || empty($ids)){
            return $this->error('请选择删除项');
        }
        try{
            User::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除用户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 启用禁用
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $parms = $request->all(['status','user_id']);
        try{
            User::query()->where('id','=',$parms['user_id'])->update(['status'=>$parms['status']]);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('设置用户状态异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function changeMyPassword(Request $request)
    {
        if ($request->ajax()){
            $params = $request->all(['old_password','password','password2']);
            if ($params['password'] !== $params['password2'] ){
                return $this->error('两次密码不一致');
            }
            $user = $request->user();
            if (!Hash::check($params['old_password'],$user->password)){
                return $this->error('原密码不正确');
            }
            try{
                $user->password = bcrypt($params['password']);
                $user->save();
                return $this->success();
            }catch (\Exception $exception){
                Log::error('更改密码异常：'.$exception->getMessage());
                return $this->error();
            }
        }
        return View::make('system.user.changeMyPassword');
    }

}
