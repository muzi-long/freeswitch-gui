<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.role.index');
    }

    public function data(Request $request)
    {
        $res = Role::paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleCreateRequest $request)
    {
        $data = $request->only(['name','display_name']);
        if (Role::create($data)){
            return redirect()->to(route('admin.role'))->with(['status'=>'添加角色成功']);
        }
        return redirect()->to(route('admin.role'))->withErrors('系统错误');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.role.edit',compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, $id)
    {
        $role = Role::findOrFail($id);
        $data = $request->only(['name','display_name']);
        if ($role->update($data)){
            return redirect()->to(route('admin.role'))->with(['status'=>'更新角色成功']);
        }
        return redirect()->to(route('admin.role'))->withErrors('系统错误');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Role::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 分配权限
     */
    public function permission(Request $request,$id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::with('allChilds')->where('parent_id',0)->get();
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
        return view('admin.role.permission',compact('role','permissions'));
    }

    /**
     * 存储权限
     */
    public function assignPermission(Request $request,$id)
    {
        $role = Role::findOrFail($id);
        $permissions = $request->get('permissions');

        if (empty($permissions)){
            $role->permissions()->detach();
            return redirect()->to(route('admin.role'))->with(['status'=>'已更新角色权限']);
        }
        $role->syncPermissions($permissions);
        return redirect()->to(route('admin.role'))->with(['status'=>'已更新角色权限']);
    }

}
