<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Call\Freeswitch\StoreRequest;
use App\Http\Requests\Backend\Call\Freeswitch\UpdateRequest;
use App\Models\Freeswitch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class FreeswitchController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Freeswitch::query()
                ->orderBy('id','desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        return View::make('backend.call.freeswitch.index');
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('backend.call.freeswitch.create');
    }

    /**
     * 添加
     * @param StoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'name',
            'external_ip',
            'internal_ip',
            'esl_port',
            'esl_password',
            'internal_sip_port',
            'swoole_http_port',
            'fs_install_path',
            'fs_record_path',
        ]);
        try {
            Freeswitch::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.freeswitch')]);
        }catch (\Exception $exception){
            Log::error('添加fs异常：'.$exception,$data);
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
        $model = Freeswitch::findOrFail($id);
        return View::make('backend.call.freeswitch.edit',compact('model'));
    }

    /**
     * 更新
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request,$id)
    {
        $model = Freeswitch::findOrFail($id);
        $data = $request->all([
            'name',
            'external_ip',
            'internal_ip',
            'esl_port',
            'esl_password',
            'internal_sip_port',
            'swoole_http_port',
            'fs_install_path',
            'fs_record_path',
        ]);
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.call.freeswitch')]);
        }catch (\Exception $exception){
            Log::error('更新fs异常：'.$exception,$data);
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
        if (!is_array($ids) || empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            Freeswitch::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除fs异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
