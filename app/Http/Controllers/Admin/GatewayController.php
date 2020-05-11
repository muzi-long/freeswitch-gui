<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Gateway\GatewayRequest;
use App\Models\Gateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class GatewayController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Gateway::orderByDesc('id')->paginate($request->get('limit', 30));
            foreach ($res->items() as $d){
                $d->status = null;
            }
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        return view('admin.gateway.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.gateway.create');
    }

    /**
     * @param GatewayRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(GatewayRequest $request)
    {
        $data = $request->except(['_method','_token']);
        try{
            Gateway::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::error('添加网关异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Gateway::findOrFail($id);
        return view('admin.gateway.edit',compact('model'));
    }

    /**
     * @param GatewayRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(GatewayRequest $request, $id)
    {
        $model = Gateway::findOrFail($id);
        $data = $request->except(['_method','_token']);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('更新网关异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Gateway::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 更新配置
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateXml()
    {
        set_time_limit(0);
        $gateway = Gateway::get()->toArray();
        if (empty($gateway)){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            $client = new Client();
            $res = $client->post(config('freeswitch.swoole_http_url.gateway'),['form_params'=>['data'=>json_encode($gateway)]]);
            return response()->json(json_decode($res->getBody(),true));
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }
    }

}
