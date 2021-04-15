<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CdrController extends Controller
{

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all([
                'merchant_id',
                'caller',
                'callee',
                'calltime_start',
                'calltime_end',
            ]);
            $res = Cdr::query()
                ->when($data['merchant_id'],function ($q) use ($data){
                    return $q->where('merchant_id',$data['merchant_id']);
                })
                ->when($data['caller'],function ($q) use ($data){
                    return $q->where('caller',$data['caller']);
                })
                ->when($data['callee'],function ($q) use ($data){
                    return $q->where('callee',$data['callee']);
                })
                ->when($data['calltime_start']&&!$data['calltime_end'],function ($q) use ($data){
                    return $q->where('call_time','>=',$data['calltime_start']);
                })
                ->when(!$data['calltime_start']&&$data['calltime_end'],function ($q) use ($data){
                    return $q->where('call_time','<=',$data['calltime_end']);
                })
                ->when($data['calltime_start']&&$data['calltime_end'],function ($q) use ($data){
                    return $q->whereBetween('call_time',[$data['calltime_start'],$data['calltime_end']]);
                })
                ->orderBy('call_time','desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        $merchants = Merchant::get();
        return View::make('backend.call.cdr.index',compact('merchants'));
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
        try{
            Cdr::destroy($ids);
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除话单异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
