<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\Call\Dialplan\ConditionRequest;
use App\Models\Condition;
use App\Models\Extension;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ConditionController extends Controller
{

    public function index(Request $request,$extension_id)
    {
        if ($request->ajax()){
            $res = Condition::where('extension_id',$extension_id)->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        $extension = Extension::findOrFail($extension_id);
        return View::make('backend.call.dialplan.condition.index',compact('extension'));
    }

    public function create($extension_id)
    {
        $extension = Extension::findOrFail($extension_id);

        return View::make('backend.call.dialplan.condition.create',compact('extension'));
    }

    public function store(ConditionRequest $request,$extension_id)
    {
        $extension = Extension::findOrFail($extension_id);
        $data = $request->all(['display_name','field','expression','break','sort']);
        $data['extension_id'] = $extension->id;
        try {
            Condition::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.condition',['extension_id'=>$extension->id])]);
        }catch (\Exception $exception){
            Log::error('添加拨号规则异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }


    public function edit($extension_id,$id)
    {
        $extension = Extension::findOrFail($extension_id);
        $model = Condition::findOrFail($id);
        return View::make('backend.call.dialplan.condition.edit',compact('extension','model'));
    }

    public function update(Request $request, $extension_id, $id)
    {
        $extension = Extension::findOrFail($extension_id);
        $model = Condition::findOrFail($id);
        $data = $request->all(['display_name','field','expression','break','sort']);
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.call.condition',['extension_id'=>$extension->id])]);
        }catch (\Exception $exception){
            Log::error('更新拨号规则异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }


    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Condition::destroy($ids)){
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }
        return Response::json(['code'=>1,'msg'=>'删除失败']);
    }
}
