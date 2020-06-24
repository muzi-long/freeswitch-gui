<?php

namespace App\Http\Controllers\Backend;

use App\Models\Action;
use App\Models\Condition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ActionController extends Controller
{

    public function index(Request $request,$condition_id)
    {
        if ($request->ajax()){
            $res = Action::where('condition_id',$condition_id)->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        $condition = Condition::findOrFail($condition_id);
        return View::make('backend.call.dialplan.action.index',compact('condition'));
    }

    public function create($condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        return View::make('backend.call.dialplan.action.create',compact('condition'));
    }

    public function store(Request $request,$condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        $data = $request->all(['display_name','application','data','sort']);
        $data['condition_id'] = $condition->id;
        try {
            Action::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.action',['condition_id'=>$condition->id])]);
        }catch (\Exception $exception){
            Log::error('添加拨号应用异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }


    public function edit($condition_id,$id)
    {
        $condition = Condition::findOrFail($condition_id);
        $model = Action::findOrFail($id);
        return View::make('backend.call.dialplan.action.edit',compact('condition','model'));
    }


    public function update(Request $request, $condition_id, $id)
    {
        $condition = Condition::findOrFail($condition_id);
        $model = Action::findOrFail($id);
        $data = $request->all(['display_name','application','data','sort']);
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.call.action',['condition_id'=>$condition->id])]);
        }catch (\Exception $exception){
            Log::error('更新拨号应用异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Action::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
