<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Call\Gateway\StoreRequest;
use App\Http\Requests\Backend\Call\Gateway\UpdateRequest;
use App\Models\Freeswitch;
use App\Models\Gateway;
use App\Models\Merchant;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class GatewayController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $res = Gateway::with(['freeswitch','merchant'])
                ->orderBy('id', 'desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items()
            ];
            return Response::json($data);
        }
        $fs = Freeswitch::orderBy('id', 'desc')->get();
        return View::make('backend.call.gateway.index',compact('fs'));
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.call.gateway.create',compact('merchants'));
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
            'realm',
            'username',
            'password',
            'prefix',
            'outbound_caller_id',
            'type',
            'merchant_id',
        ]);
        $merchant = Merchant::find($data['merchant_id']);
        if ($merchant == null || $merchant->freeswitch_id == 0) {
            return Response::json(['code' => 1, 'msg' => '商户未配置服务器无法添加网关']);
        }
        //验证是否超过商户最大网关数量
        $count = Gateway::where('merchant_id',$merchant->id)->count();
        if ($merchant->gateway_num - $count < 1){
            return Response::json(['code'=>1,'msg'=>'超出商户最大风头数量【'.$merchant->gateway_num.'】']);
        }
        $data['freeswitch_id'] = $merchant->freeswitch_id;
        try {
            Gateway::create($data);
            return Response::json(['code' => 0, 'msg' => '添加成功', 'url' => route('backend.call.gateway')]);
        } catch (\Exception $exception) {
            Log::error('添加网关异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '添加失败']);
        }
    }

    /**
     * 添加
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Gateway::findOrFail($id);
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.call.gateway.edit', compact('model','merchants'));
    }

    /**
     * 添加
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $id)
    {
        $data = $request->all([
            'name',
            'realm',
            'username',
            'password',
            'prefix',
            'outbound_caller_id',
            'type',
        ]);
        $model = Gateway::findOrFail($id);
        try {
            $model->update($data);
            return Response::json(['code' => 0, 'msg' => '更新成功', 'url' => route('backend.call.gateway')]);
        } catch (\Exception $exception) {
            Log::error('更新网关异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '更新失败']);
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
        if (!is_array($ids) || empty($ids)) {
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        try {
            Gateway::destroy($ids);
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            Log::error('删除网关异常：' . $exception->getMessage());
            return Response::json(['code' => 1, 'msg' => '删除失败']);
        }
    }

    /**
     * 更新配置文件
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateXml(Request $request)
    {
        $fs_id = $request->input('fs_id');
        $fs = Freeswitch::find($fs_id);
        if ($fs == null) {
            return response()->json(['code' => 1, 'msg' => '请选择服务器']);
        }
        $gateway = Gateway::where('freeswitch_id', $fs_id)->get()->toArray();
        if (empty($gateway)) {
            return response()->json(['code' => 1, 'msg' => '无数据需要更新']);
        }
        try {
            $client = new Client();
            $res = $client->post('http://' . $fs->internal_ip . ':' . $fs->swoole_http_port . '/gateway', [
                'form_params' => [
                    'data' => json_encode($gateway),
                    'conf' => json_encode([
                        'host' => $fs->internal_ip,
                        'port' => $fs->esl_port,
                        'password' => $fs->esl_password,
                        'path' => $fs->fs_install_path,
                    ]),
                ],
                'timeout' => 10,
            ]);
            return Response::json(json_decode($res->getBody(), true));
        } catch (\Exception $exception) {
            Log::error('更新网关配置异常：' . $exception->getMessage(), $gateway);
            return Response::json(['code' => 1, 'msg' => '更新失败']);
        }
    }

}
