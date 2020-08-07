<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class DepartmentController extends Controller
{

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Department::with('childs')
                ->where('merchant_id',$request->user()->merchant_id)
                ->orderBy('sort','asc')
                ->orderBy('id','desc')
                ->get();
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->count(),
                'data' => $res
            ];
            return Response::json($data);
        }
        return View::make('frontend.account.department.index');
    }

    /**
     * 添加部门
     * @return \Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $departments = Department::with('childs')
            ->where('parent_id',0)
            ->where('merchant_id',$request->user()->merchant_id)
            ->get();
        return View::make('frontend.account.department.create',compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->all(['name','parent_id','sort']);
        $data['merchant_id'] = $request->user()->merchant_id;

        try{
            Department::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('frontend.account.department')]);
        }catch (\Exception $exception){
            Log::error('添加部门异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * 更新
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Department::findOrFail($id);
        $departments = Department::with('childs')
            ->where('parent_id',0)
            ->where('merchant_id',$model->merchant_id)
            ->get();
        return View::make('frontend.account.department.edit',compact('departments','model'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','parent_id','sort']);
        $model = Department::findOrFail($id);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('frontend.account.department')]);
        }catch (\Exception $exception){
            Log::error('添加部门异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        $model = Department::with('childs')->where('merchant_id',$request->user()->merchant_id)->find($ids[0]);
        if (!$model) {
            return Response::json(['code' => 1, 'msg' => '部门不存在']);
        }
        if ($model->childs->isNotEmpty()) {
            return Response::json(['code' => 1, 'msg' => '存在子部门禁止删除']);
        }
        try {
            $model->delete();
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            Log::error('删除部门异常：'.$exception->getMessage());
            return Response::json(['code' => 1, 'msg' => '删除失败']);
        }
    }

}
