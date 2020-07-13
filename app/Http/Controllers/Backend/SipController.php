<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Call\Sip\StoreRequest;
use App\Http\Requests\Backend\Call\Sip\UpdateRequest;
use App\Models\Merchant;
use App\Models\Sip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class SipController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){

        }
        return View::make('backend.call.sip.index');
    }

    public function create()
    {
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.call.sip.create',compact('merchants'));
    }

    public function store(StoreRequest $request)
    {
        $data = $request->all([
            'username',
            'password',
            'merchant_id',
            'gateway_id',
        ]);
        $merchant = Merchant::find($data['merchant_id']);
        if ($merchant == null || $merchant->freeswitch_id == 0) {
            return Response::json(['code' => 1, 'msg' => '商户未配置服务器无法添加网关']);
        }
        //验证是否超过商户最大分机数量
        $count = Sip::where('merchant_id',$data['merchantiglk_id'])->count();
        if ($merchant->sip_num - $count < 1){
            return Response::json(['code'=>1,'msg'=>'超出商户最大分机数量【'.$merchant->sip_num.'】']);
        }
        $data['freeswitch_id'] = $merchant->freeswitch_id;
        try {
            Sip::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.call.sip')]);
        }catch (\Exception $exception){
            Log::error('添加分机异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    public function edit($id)
    {
        $model = Sip::findOrFail($id);
        return View::make('backend.call.sip.edit',compact('model'));
    }

    public function update(UpdateRequest $request,$id)
    {
        $model = Sip::findOrFail($id);
        $data = $request->all([
            'username',
            'password',
            'gateway_id',
        ]);
        try {
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.call.sip')]);
        }catch (\Exception $exception){
            Log::error('更新分机异常：'.$exception->getMessage(),$data);
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
            Sip::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除分机异常：'.$exception->getMessage(),$ids);
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }


}
