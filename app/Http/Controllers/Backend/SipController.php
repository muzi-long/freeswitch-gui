<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Call\Sip\ListStoreRequest;
use App\Http\Requests\Backend\Call\Sip\StoreRequest;
use App\Http\Requests\Backend\Call\Sip\UpdateRequest;
use App\Models\Freeswitch;
use App\Models\Gateway;
use App\Models\Merchant;
use App\Models\Rate;
use App\Models\Sip;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class SipController extends Controller
{

    /**
     * 分机列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all(['freeswitch_id','merchant_id','username']);
            $res = Sip::with(['freeswitch', 'merchant','gateway'])
                ->when($data['freeswitch_id'],function ($q) use($data){
                    return $q->where('freeswitch_id',$data['freeswitch_id']);
                })
                ->when($data['merchant_id'],function ($q) use($data){
                    return $q->where('merchant_id',$data['merchant_id']);
                })
                ->when($data['username'],function ($q) use($data){
                    return $q->where('username',$data['username']);
                })
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
        $merchants = Merchant::orderBy('id', 'desc')->get();
        $fs = Freeswitch::orderBy('id', 'desc')->get();
        return View::make('backend.call.sip.index', compact('merchants', 'fs'));
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $merchants = Merchant::orderBy('id', 'desc')->get();
        $rates = Rate::orderByDesc('id')->get();
        return View::make('backend.call.sip.create', compact('merchants','rates'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'username',
            'password',
            'merchant_id',
            'gateway_id',
            'rate_id',
        ]);
        $merchant = Merchant::find($data['merchant_id']);
        if ($merchant == null || $merchant->freeswitch_id == 0) {
            return Response::json(['code' => 1, 'msg' => '商户未配置服务器无法添加网关']);
        }
        //验证是否超过商户最大分机数量
        $count = Sip::where('merchant_id', $data['merchant_id'])->count();
        if ($merchant->sip_num - $count < 1) {
            return Response::json(['code' => 1, 'msg' => '超出商户最大分机数量【' . $merchant->sip_num . '】']);
        }
        $data['freeswitch_id'] = $merchant->freeswitch_id;
        try {
            Sip::create($data);
            return Response::json(['code' => 0, 'msg' => '添加成功', 'url' => route('backend.call.sip')]);
        } catch (\Exception $exception) {
            Log::error('添加分机异常：' . $exception->getMessage(), $data);
            return Response::json(['code' => 1, 'msg' => '添加失败']);
        }
    }

    /**
     * 批量添加
     * @return \Illuminate\Contracts\View\View
     */
    public function createList()
    {
        $merchants = Merchant::orderBy('id', 'desc')->get();
        $rates = Rate::orderByDesc('id')->get();
        return View::make('backend.call.sip.create_list', compact('merchants','rates'));
    }

    public function storeList(ListStoreRequest $request)
    {
        $data = $request->all(['sip_start','sip_end','password','gateway_id','merchant_id','rate_id']);

        if ($data['sip_start'] <= $data['sip_end']){
            //验证是否超过商户最大分机数量
            $merchant = Merchant::find($data['merchant_id']);
            $count = Sip::where('merchant_id', $data['merchant_id'])->count();
            if ($merchant->sip_num - ($data['sip_end'] - $data['sip_start']+1) < 1) {
                return Response::json(['code' => 1, 'msg' => '超出商户最大分机数量【' . $merchant->sip_num . '】']);
            }

            //开启事务
            DB::beginTransaction();
            try{
                for ($i=$data['sip_start'];$i<=$data['sip_end'];$i++){
                    DB::table('sip')->insert([
                        'freeswitch_id' => $merchant->freeswitch_id,
                        'merchant_id' => $data['merchant_id'],
                        'gateway_id' => $data['gateway_id'],
                        'rate_id' => $data['rate_id'],
                        'username'  => $i,
                        'password'  => $data['password'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                DB::commit();
                return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.sip')]);
            }catch (\Exception $exception) {
                DB::rollback();
                Log::error('批量添加分机异常：'.$exception->getMessage(),$data);
                return Response::json(['code'=>1,'msg'=>'添加失败']);
            }
        }
        return Response::json(['code'=>1,'msg'=>'开始分机号必须小于等于结束分机号']);
    }

    /**
     * 更新
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Sip::findOrFail($id);
        $merchants = Merchant::orderBy('id', 'desc')->get();
        $gateways = Gateway::where('merchant_id',$model->merchant_id)->get();
        $rates = Rate::orderByDesc('id')->get();
        return View::make('backend.call.sip.edit', compact('model','merchants','gateways','rates'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $model = Sip::findOrFail($id);
        $data = $request->all([
            'username',
            'password',
            'gateway_id',
            'rate_id',
        ]);
        try {
            $model->update($data);
            return Response::json(['code' => 0, 'msg' => '更新成功', 'url' => route('backend.call.sip')]);
        } catch (\Exception $exception) {
            Log::error('更新分机异常：' . $exception->getMessage(), $data);
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
        if (empty($ids)) {
            return Response::json(['code' => 1, 'msg' => '请选择删除项']);
        }
        try {
            Sip::destroy($ids);
            return Response::json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $exception) {
            Log::error('删除分机异常：' . $exception->getMessage(), $ids);
            return Response::json(['code' => 1, 'msg' => '删除失败']);
        }
    }


    /**
     * 更新配置
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateXml(Request $request){
        $fs_id = $request->input('freeswitch_id');
        $fs = Freeswitch::find($fs_id);
        if ($fs == null) {
            return Response::json(['code' => 1, 'msg' => '请选择服务器']);
        }
        $sips = DB::table('sip')->where('freeswitch_id',$fs_id)->get()->toArray();
        if (empty($sips)){
            return Response::json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            $client = new Client();
            $res = $client->post('http://' . $fs->internal_ip . ':' . $fs->swoole_http_port . '/directory', [
                'form_params' => [
                    'data' => json_encode($sips),
                    'conf' => json_encode([
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
            return Response::json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }
    }

}
