<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\OrderPay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class PayController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = OrderPay::with('order')
                ->orderBy('status','asc')
                ->orderByDesc('created_at')
                ->paginate($request->input('limit',30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('account.pay.index');
    }

    public function check(Request $request)
    {
        $model = OrderPay::with('order')->where('id','=',$request->input('id'))->first();
        if ($request->ajax()){
            $data = $request->all(['check_result','status']);
            if ($model->order==null){
                return $this->error('订单已不存在');
            }
            DB::beginTransaction();
            try {
                if ($data['status']==2&&!$data['check_result']){
                    return $this->error('请备注审核未通过原因');
                }
                $model->update([
                    'check_result' => $data['check_result'],
                    'status' => $data['status'],
                    'check_user_id' => $request->user()->id,
                    'check_user_nickname' => $request->user()->nickname,
                    'check_time' => date('Y-m-d H:i:s'),
                ]);
                if ($data['status']==1){
                    $payed_money = $model->order->payed_money + $model->money;
                    if ($payed_money >= $model->order->total_money){
                        $model->order->update([
                            'payed_money' => $payed_money,
                            'status' => 1,
                        ]);
                    }else{
                        $model->order->update([
                            'payed_money' => $payed_money,
                        ]);
                    }
                }
                DB::commit();
                return $this->success();
            }catch (\Exception $exception){
                DB::rollBack();
                Log::error('审核异常：'.$exception->getMessage());
                return $this->error();
            }
        }
        return View::make('account.pay.check',compact('model'));
    }

}
