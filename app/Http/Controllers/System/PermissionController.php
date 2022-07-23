<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class PermissionController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Permission::query()->orderBy('id','desc')->get();
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->count(),
                'data' => $res
            ];
            return $this->success("ok",$res,$res->count());
        }
        return View::make('system.permission.index');
    }

    /**
     * 添加权限
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $permissions = Permission::with('childs')->where('parent_id', 0)->get();
        return View::make('system.permission.create', compact('permissions'));
    }

    /**
     * 添加权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all(['name','display_name','parent_id']);
        try {
            Permission::create($data);
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('添加权限异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 更新权限
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        $permissions = Permission::with('childs')->where('parent_id', 0)->get();
        return View::make('system.permission.edit', compact('permission', 'permissions'));
    }

    /**
     * 更新权限
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $request->all(['name','display_name','parent_id']);
        try {
            $permission->update($data);
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('更新权限异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    /**
     * 删除权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (!is_array($ids) || empty($ids)) {
            return $this->error('请选择删除项');
        }
        $permission = Permission::with('childs')->find($ids[0]);
        if (!$permission) {
            return $this->error('权限不存在');
        }
        //如果有子权限，则禁止删除
        if ($permission->childs->isNotEmpty()) {
            return $this->error('存在子权限禁止删除');
        }
        try {
            $permission->delete();
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('删除权限异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
