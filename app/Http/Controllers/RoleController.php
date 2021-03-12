<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class RoleController extends Controller
{
    /**
     * 角色列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Role::query()->orderByDesc('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('role.index');
    }

    /**
     * 添加角色
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('role.create');
    }

    /**
     * 添加角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all(['name','display_name','permission_ids']);
        $data['permission_ids'] = $data['permission_ids'] == null ? [] : $data['permission_ids'];
        try{
            $role = Role::create($data);
            $role->syncPermissions($data['permission_ids']);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加角色异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 更新角色
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return View::make('role.edit',compact('role'));
    }

    /**
     * 更新角色
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->all(['name','display_name','permission_ids']);
        $data['permission_ids'] = $data['permission_ids'] == null ? [] : $data['permission_ids'];
        try{
            $role->update($data);
            $role->syncPermissions($data['permission_ids']);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新角色异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 删除角色
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)){
            return $this->error();
        }
        try{
            Role::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除角色异常：'.$exception->getMessage());
            return $this->success();
        }
    }

}
