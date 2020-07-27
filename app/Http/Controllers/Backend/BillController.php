<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
                'merchant_id',
                'type',
                'created_at_start',
                'created_at_end',
            ]);
            $res = Bill::query()
                ->when($data['merchant_id'],function ($q) use($data){
                    return $q->where('merchant_id',$data['merchant_id']);
                })
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
        $merchants = Merchant::get();
        return View::make('backend.platform.bill.index',compact('merchants'));
    }

    public function store(Request $request)
    {
        $data = $request->all([
            'merchant_id',
            'type',
            'money',
            'remark',
        ]);
        if ($data['money'] < 0.01){
            return Response::json(['code'=>1,'msg'=>'请填写正确的金额，最小为0.01元']);
        }
        $merchant = Merchant::find($data['merchant_id']);
        //将元转为分
        $data['money'] = 100*$data['money'];
        // 金额转换
        $change_money = $data['type'] == 1 ? abs($data['money']) : -1 * abs($data['money']);
        $data['total'] = $merchant->money + (int)$change_money;
        $data['admin_id'] = auth()->user()->id;
        $data['admin_name'] = auth()->user()->nickname;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['merchant_name'] = $merchant->company_name;
        DB::beginTransaction();
        try {
            DB::table('merchant')->where('id',$data['merchant_id'])->increment('money',$change_money);
            DB::table('bill')->insert($data);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'操作成功']);
        }catch (\Exception $exception){
            Log::error('商户帐单操作失败：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'操作失败']);
        }
    }

}
