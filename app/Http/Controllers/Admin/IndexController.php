<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\Gateway;
use App\Models\Merchant;
use App\Models\Sip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    //后台布局
    public function layout()
    {
        return View::make('admin.layout');
    }

    public function index()
    {
        //商户数量
        $merchantNum = Merchant::where('merchant_id',0)->count();
        //员工数量
        $memberNum = Merchant::where('merchant_id','!=',0)->count();
        //网关数量
        $gatewayNum = Gateway::count();
        //分机数量
        $sipNum = Sip::count();

        /*$datas = Merchant::with(['sips','info'])->whereHas('sips')->where('merchant_id',0)->get();
        foreach ($datas as $data){
            foreach ($data->sips as $sip){
                $sip->todayCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::today(),Carbon::tomorrow()])->count();
                $sip->todaySuccessCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::today(),Carbon::tomorrow()])->where('billsec','>',0)->count();
                $sip->todayRateCalls = $sip->todayCalls>0?round($sip->todaySuccessCalls/$sip->todayCalls,4)*100:0.00;

                $sip->weekCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->count();
                $sip->weekSuccessCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->where('billsec','>',0)->count();
                $sip->weekRateCalls = $sip->weekCalls>0?round($sip->weekSuccessCalls/$sip->weekCalls,4)*100:0.00;

                $sip->monthCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])->count();
                $sip->monthSuccessCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])->where('billsec','>',0)->count();
                $sip->monthRateCalls = $sip->monthCalls>0?round($sip->monthSuccessCalls/$sip->monthCalls,4)*100:0.00;
            }
        }*/
        $datas = [];
        return View::make('admin.index.index',compact('merchantNum','memberNum','gatewayNum','sipNum','datas'));
    }
}
