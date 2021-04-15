<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Backend\Call\Dialplan\ExtensionRequest;
use App\Models\Extension;
use App\Models\Freeswitch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ExtensionController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $query = Extension::query();
            $res = $query->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        $fs = Freeswitch::orderBy('id','desc')->get();
        return View::make('backend.call.dialplan.extension.index',compact('fs'));
    }


    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('backend.call.dialplan.extension.create');
    }

    /**
     * 添加
     * @param ExtensionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExtensionRequest $request)
    {
        $data = $request->all(['display_name','name','sort','continue','context']);
        try {
            Extension::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.extension')]);
        }catch (\Exception $exception){
            Log::error('添加拨号计划异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $extension = Extension::with('conditions')->findOrFail($id);
        return View::make('backend.call.dialplan.extension.show',compact('extension'));
    }

    /**
     * 更新
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $model = Extension::findOrFail($id);
        return view('backend.call.dialplan.extension.edit',compact('model'));
    }

    /**
     * 更新
     * @param ExtensionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExtensionRequest $request, $id)
    {
        $model = Extension::findOrFail($id);
        $data = $request->all(['display_name','name','sort','continue','context']);
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.call.extension')]);
        }catch (\Exception $exception){
            Log::error('更新拨号计划异常：'.$exception->getMessage(),$data);
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
        try {
            Extension::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除拨号计划异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 更新配置
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateXml(Request $request)
    {
        set_time_limit(0);
        $extension = DB::table('extension')->orderBy('sort')->get()->groupBy('context')->toArray();
        $condition = DB::table('condition')->orderBy('sort')->get()->groupBy('extension_id')->toArray();
        $action = DB::table('action')->orderBy('sort')->get()->groupBy('condition_id')->toArray();
        if (empty($extension)){
            return Response::json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        foreach ($condition as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $value2->action = isset($action[$value2->id]) ? $action[$value2->id] : [];
            }
        }
        foreach ($extension as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $value2->condition = isset($condition[$value2->id]) ? $condition[$value2->id] : [];
            }
        }
        $data = $extension;
        $fs = Freeswitch::find($request->input('fs_id'));
        try{
            $client = new Client();
            $res = $client->post('http://'.$fs->internal_ip.':'.$fs->swoole_http_port.'/dialplan',[
                'form_params'=>[
                    'data'=>json_encode($data),
                    'conf'=>json_encode([
                        'host' => $fs->internal_ip,
                        'port' => $fs->esl_port,
                        'password' => $fs->esl_password,
                        'path' => $fs->fs_install_path,
                    ]),
                ],
                'timeout' => 10,
            ]);
            return Response::json(json_decode($res->getBody(),true));
        }catch (\Exception $exception){
            Log::error('更新拨号计划配置异常:'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

}
