<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Project\ProjectDesignRequest;
use App\Models\ProjectDesign;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ProjectDesignController extends Controller
{

    /**
     * 项目表单设计列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = ProjectDesign::orderBy('sort','asc')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('admin.project_design.index');
    }


    /**
     * 添加字段
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('admin.project_design.create');
    }

    /**
     * 添加字段
     * @param ProjectDesignRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProjectDesignRequest $request)
    {
        $data = $request->all(['field_label','field_key','field_type','field_option','field_value','field_tips','sort','visiable']);
        //验证field_key是否重复
        $hasExisit = ProjectDesign::where('field_key',$data['field_key'])->count();
        if (in_array($data['field_key'],['company_name','name','phone']) || $hasExisit){
            return Response::json(['code'=>1,'msg'=>'字段Key已存在']);
        }
        try{
            if ($data['visiable']==null){
                $data['visiable'] = 2;
            }
            ProjectDesign::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::error('添加表单设计异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * 更新表单设计
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = ProjectDesign::findOrFail($id);
        return View::make('admin.project_design.edit',compact('model'));
    }

    /**
     * 更新表单设计
     * @param ProjectDesignRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(ProjectDesignRequest $request,$id)
    {
        $model = ProjectDesign::findOrFail($id);
        $data = $request->all(['field_label','field_key','field_type','field_option','field_value','field_tips','sort','visiable']);
        //验证field_key是否重复
        $hasExisit = ProjectDesign::where('field_key',$data['field_key'])->where('id','!=',$id)->count();
        if (in_array($data['field_key'],['company_name','name','phone']) || $hasExisit){
            return Response::json(['code'=>1,'msg'=>'字段Key已存在']);
        }
        if ($data['visiable']==null){
            $data['visiable'] = 2;
        }
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('更新表单设计异常：'.$exception->getMessage());
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
        $id = $ids[0];
        //删除
        DB::beginTransaction();
        try{
            DB::table('project_design')->where('id',$id)->delete();
            DB::table('project_design_value')->where('project_design_id',$id)->delete();
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除表单字段异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
