<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Permission\StoreRequest;
use App\Http\Requests\Admin\Permission\UpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use App\Models\Permission;

class PermissionController extends Controller
{
    /**
     * 权限列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Permission::orderBy('sort','asc')->orderBy('id','desc')->get();
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->count(),
                'data' => $res
            ];
            return Response::json($data);
        }
        return View::make('admin.permission.index');
    }

    /**
     * 添加权限
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $permissions = Permission::with('childs')->where('parent_id', 0)->get();
        return View::make('admin.permission.create', compact('permissions'));
    }

    /**
     * 添加权限
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all(['name','display_name','sort','parent_id']);
        try {
            Permission::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        } catch (\Exception $exception) {
            Log::error('添加权限异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
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
        return View::make('admin.permission.edit', compact('permission', 'permissions'));
    }

    /**
     * 更新权限
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $permission = Permission::findOrFail($id);
        $data = $request->all(['name','display_name','sort','parent_id']);
        try {
            $permission->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        } catch (\Exception $exception) {
            Log::error('更新权限异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
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
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        $permission = Permission::with('childs')->find($ids[0]);
        if (!$permission) {
            return Response::json(['code' => 1, 'msg' => '权限不存在']);
        }
        //如果有子权限，则禁止删除
        if ($permission->childs->isNotEmpty()) {
            return Response::json(['code' => 1, 'msg' => '存在子权限禁止删除']);
        }
        try {
            $permission->delete();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            Log::error('删除权限异常：'.$exception->getMessage());
            return Response::json(['code' => 1, 'msg' => '删除失败']);
        }
    }
}
