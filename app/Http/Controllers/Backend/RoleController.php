<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\System\Role\StoreRequest;
use App\Http\Requests\Backend\System\Role\UpdateRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
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
            $res = Role::where('guard_name','=',config('freeswitch.backend_guard'))->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('backend.system.role.index');
    }

    /**
     * 添加角色
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('backend.system.role.create');
    }

    /**
     * 添加角色
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->only(['name','display_name']);
        try{
            Role::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::error('添加角色异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
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
        return View::make('backend.system.role.edit',compact('role'));
    }

    /**
     * 更新角色
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->only(['name','display_name']);
        try{
            $role->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);

        }catch (\Exception $exception){
            Log::error('更新角色异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
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
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            Role::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除角色异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 分配权限
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function permission($id)
    {
        $role = Role::where('guard_name',config('freeswitch.backend_guard'))->findOrFail($id);
        $permissions = Permission::with('childs')->where('guard_name',$role->guard_name)->where('parent_id',0)->get();
        foreach ($permissions as $p1){
            $p1->own = $role->hasPermissionTo($p1->id) ? 'checked' : false ;
            if ($p1->childs->isNotEmpty()){
                foreach ($p1->childs as $p2){
                    $p2->own = $role->hasPermissionTo($p2->id) ? 'checked' : false ;
                    if ($p2->childs->isNotEmpty()){
                        foreach ($p2->childs as $p3){
                            $p3->own = $role->hasPermissionTo($p3->id) ? 'checked' : false ;
                        }
                    }
                }
            }
        }
        return View::make('backend.system.role.permission',compact('role','permissions'));
    }

    /**
     * 存储权限
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignPermission(Request $request,$id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->get('permissions',[]);
        try{
            $role->syncPermissions($permissions);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('更新角色权限异常：'.$exception->getMessage());
            return Response::json(['code'=>0,'msg'=>'更新失败']);
        }
    }
}
