<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class BillController extends Controller
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
                'type',
                'created_at_start',
                'created_at_end',
            ]);
            $res = Bill::query()
                ->where('merchant_id',$request->user()->merchant_id)
                ->when($data['type'],function ($q) use($data){
                    return $q->where('type',$data['type']);
                })
                ->when($data['created_at_start']&&!$data['created_at_end'],function ($q) use($data){
                    return $q->where('created_at','>=',$data['created_at_start']);
                })
                ->when(!$data['created_at_start']&&$data['created_at_end'],function ($q) use($data){
                    return $q->where('created_at','<=',$data['created_at_end']);
                })
                ->when($data['created_at_start']&&$data['created_at_end'],function ($q) use($data){
                    return $q->whereBetween('created_at',[$data['created_at_start'],$data['created_at_end']]);
                })
                ->orderBy('id','desc')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        return View::make('frontend.account.bill.index');
    }
}
