<?php

namespace App\Http\Controllers\Crm;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Department::orderBy('id')->get();
            return $this->success('ok',$res,$res->count());
        }
        return View::make('crm.department.index');
    }

    public function create(Request $request)
    {
        $parent_id = $request->input('parent_id');
        return View::make('crm.department.create',compact('parent_id'));
    }

    public function store(Request $request)
    {
        $data = $request->all(['name','parent_id','user_id']);
        if ($data['parent_id']){
            $parent = Department::query()->where('id',$data['parent_id'])->first();
            if ($parent == null){
                return $this->error('上级部门不存在');
            }
            $data['level'] = $parent->level + 1;
        }
        try{
            $user = User::query()->where('id','=',$data['user_id'])->first();
            Department::create([
                'name' => $data['name'],
                'parent_id' => $data['parent_id'],
                'business_user_id' => $user->id??0,
                'business_user_nickname' => $user->nickname??null,
            ]);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加部门异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = Department::findOrFail($id);
        return View::make('crm.department.edit',compact('model'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','user_id']);
        $model = Department::findOrFail($id);
        try{
            $user = User::query()->where('id','=',$data['user_id'])->first();
            $model->update([
                'name' => $data['name'],
                'business_user_id' => $user->id??0,
                'business_user_nickname' => $user->nickname??null,
            ]);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新部门异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return $this->error('请选择删除项');
        }
        $model = Department::with('childs')->find($ids[0]);
        if (!$model) {
            return $this->error('部门不存在');
        }
        if ($model->childs->isNotEmpty()) {
            return $this->error('存在子部门禁止删除');
        }
        try {
            $model->delete();
            return $this->success();
        } catch (\Exception $exception) {
            Log::error('删除部门异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
