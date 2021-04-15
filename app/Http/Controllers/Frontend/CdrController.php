<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\Merchant;
use Illuminate\Http\Request;
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
                'caller',
                'callee',
                'calltime_start',
                'calltime_end',
            ]);
            $user = $request->user();
            $res = Cdr::query()
                ->where(function ($query) use($user) {
                    if ($user->hasPermissionTo('frontend.call.cdr.merchant')) {
                        return $query->where('merchant_id',$user->merchant_id);
                    }elseif ($user->hasPermissionTo('frontend.call.cdr.department')) {
                        return $query->where('merchant_id',$user->merchant_id)->where('department_id',$user->department_id);
                    }else{
                        return $query->where('staff_id',$user->id);
                    }
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
        return View::make('frontend.call.cdr.index');
    }
}
