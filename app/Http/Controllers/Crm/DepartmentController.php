<?php

namespace App\Http\Controllers\Crm;

use App\Models\Department;
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

    public function create()
    {
        $departments = Department::with('childs')->where('parent_id',0)->get();
        return View::make('department.create',compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->all(['name','parent_id']);
        try{
            Department::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加部门异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = Department::findOrFail($id);
        $departments = Department::with('childs')->where('parent_id',0)->get();
        return View::make('crm.department.edit',compact('departments','model'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','parent_id']);
        $model = Department::findOrFail($id);
        try{
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加部门异常：'.$exception->getMessage());
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
