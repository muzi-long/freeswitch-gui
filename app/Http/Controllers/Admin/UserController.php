<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Permission;
use App\Models\Sip;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.user.index');
    }

    public function data(Request $request)
    {
        $res = User::paginate($request->get('limit', 30));
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
        return view('admin.user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $data = $request->all();
        $data['uuid'] = \Faker\Provider\Uuid::uuid();
        $data['password'] = bcrypt($data['password']);
        if (User::create($data)){
            return redirect()->to(route('admin.user'))->with(['success'=>'添加用户成功']);
        }
        return redirect()->to(route('admin.user'))->withErrors('系统错误');
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
        $user = User::findOrFail($id);
        return view('admin.user.edit',compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->except('password');
        if ($request->get('password')){
            $data['password'] = bcrypt($request->get('password'));
        }
        if ($user->update($data)){
            return redirect()->to(route('admin.user'))->with(['success'=>'更新用户成功']);
        }
        return redirect()->to(route('admin.user'))->withErrors('系统错误');
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
        if (User::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 分配角色
     */
    public function role(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $roles = Role::get();
        $hasRoles = $user->roles();
        foreach ($roles as $role){
            $role->own = $user->hasRole($role) ? true : false;
        }
        return view('admin.user.role',compact('roles','user'));
    }

    /**
     * 更新分配角色
     */
    public function assignRole(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $roles = $request->get('roles',[]);
       if ($user->syncRoles($roles)){
           return redirect()->to(route('admin.user'))->with(['success'=>'更新用户角色成功']);
       }
        return redirect()->to(route('admin.user'))->withErrors('系统错误');
    }

    /**
     * 分配权限
     */
    public function permission(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $permissions = Permission::with('allChilds')->where('parent_id',0)->get();
        foreach ($permissions as $p1){
            $p1->own = $user->hasDirectPermission($p1->id) ? 'checked' : false ;
            if ($p1->childs->isNotEmpty()){
                foreach ($p1->childs as $p2){
                    $p2->own = $user->hasDirectPermission($p2->id) ? 'checked' : false ;
                    if ($p2->childs->isNotEmpty()){
                        foreach ($p2->childs as $p3){
                            $p3->own = $user->hasDirectPermission($p3->id) ? 'checked' : false ;
                        }
                    }
                }
            }
        }
        return view('admin.user.permission',compact('user','permissions'));
    }

    /**
     * 存储权限
     */
    public function assignPermission(Request $request,$id)
    {
        $user = User::findOrFail($id);
        $permissions = $request->get('permissions');

        if (empty($permissions)){
            $user->permissions()->detach();
            return redirect()->to(route('admin.user'))->with(['success'=>'已更新用户直接权限']);
        }
        $user->syncPermissions($permissions);
        return redirect()->to(route('admin.user'))->with(['success'=>'已更新用户直接权限']);
    }

    /**
     * 修改密码表单
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changeMyPasswordForm()
    {
        return view('admin.user.changeMyPassword');
    }

    /**
     * 修改密码逻辑
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeMyPassword(Request $request)
    {
        $this->validate($request,[
            'old_password' => 'required|string|min:6|max:14',
            'new_password' => 'required|string|min:6|max:14|confirmed'
        ]);
        //验证原密码
        if (!Hash::check($request->get('old_password'),auth()->user()->getAuthPassword())){
            return back()->withInput()->withErrors('原密码不正确');
        }
        //更新密码
        if ($request->user()->fill(['password' => Hash::make($request->new_password)])->save()){
            return back()->with(['success'=>'密码修改成功']);
        }
        return back()->withErrors('修改密码失败');
    }

}
