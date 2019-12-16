<?php

namespace App\Http\Controllers\Home;

use App\Http\Requests\Home\SipRequest;
use App\Models\Cdr;
use App\Models\Merchant;
use App\Models\Sip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class SipController extends Controller
{
    /**
     * 分机管理
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('home.sip.index');
    }

    public function data(Request $request)
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        $res = Sip::with('gateway')->where('merchant_id',$merchant_id)->orderByDesc('id')->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

    /**
     * 添加分机
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        $merchant = Merchant::with('gateways')->findOrFail($merchant_id);
        return View::make('home.sip.create',compact('merchant'));
    }

    /**
     * 添加分机
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(SipRequest $request)
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        $data = $request->all([
            'gateway_id',
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        $data['merchant_id'] = $merchant_id;

        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        //验证商户允许的最大分机数
        $merchant = Merchant::with('info')->withCount('sips')->findOrFail($data['merchant_id']);
        if ($merchant->sips_count >= $merchant->info->sip_num){
            return back()->withInput()->withErrors(['error'=>'添加失败：超出商户最大允许分机数量']);
        }
        try{
            Sip::create($data);
            return redirect(route('home.sip'))->with(['success'=>'添加成功']);
        }catch (\Exception $exception){
            return back()->withInput()->withErrors(['error'=>'添加失败：'.$exception->getMessage()]);
        }
    }

    /**
     * 更新分机
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        $model = Sip::findOrFail($id);
        $merchant = Merchant::with('gateways')->findOrFail($merchant_id);
        return view('home.sip.edit',compact('model','merchant'));
    }

    /**
     * 更新分机
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(SipRequest $request,$id)
    {
        $model = Sip::findOrFail($id);
        $data = $request->all([
            'gateway_id',
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        try{
            $model->update($data);
            return redirect(route('home.sip'))->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            return back()->withErrors(['error'=>'更新失败：'.$exception->getMessage()]);
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
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Sip::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 分机统计
     * @return \Illuminate\Contracts\View\View
     */
    public function count()
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        /*$sips = Sip::with('merchant')->where('merchant_id',$merchant_id)->get();
        foreach ($sips as $sip){
            $sip->todayCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::today(),Carbon::tomorrow()])->count();
            $sip->todaySuccessCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::today(),Carbon::tomorrow()])->where('billsec','>',0)->count();
            $sip->todayRateCalls = $sip->todayCalls>0?round($sip->todaySuccessCalls/$sip->todayCalls,4)*100:0.00;

            $sip->weekCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->count();
            $sip->weekSuccessCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek()])->where('billsec','>',0)->count();
            $sip->weekRateCalls = $sip->weekCalls>0?round($sip->weekSuccessCalls/$sip->weekCalls,4)*100:0.00;

            $sip->monthCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])->count();
            $sip->monthSuccessCalls = Cdr::where('src',$sip->username)->whereBetween('start_at',[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth()])->where('billsec','>',0)->count();
            $sip->monthRateCalls = $sip->monthCalls>0?round($sip->monthSuccessCalls/$sip->monthCalls,4)*100:0.00;
        }*/
        $sips = [];
        return View::make('home.sip.count',compact('sips'));
    }

}
