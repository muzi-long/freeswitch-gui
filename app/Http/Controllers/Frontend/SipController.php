<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Sip;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class SipController extends Controller
{

    /**
     * 分机管理列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all(['username']);
            $res = Sip::with(['freeswitch','staff'])
                ->when($data['username'],function ($q) use($data){
                    return $q->where('username',$data['username']);
                })
                ->where('merchant_id',$request->user()->merchant_id)
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
        return View::make('frontend.call.sip.index');
    }

    public function bindForm(Request $request,$id)
    {
        $sip = Sip::findOrFail($id);
        $sipBindStaffIds = Sip::where('merchant_id',$request->user()->merchant_id)->pluck('staff_id')->toArray();
        $staffs = Staff::where('merchant_id',$request->user()->merchant_id)
            ->where('is_merchant',0)
            ->whereNotIn('id',$sipBindStaffIds)
            ->get();
        return View::make('frontend.call.sip.bind',compact('sip','staffs'));
    }

    /**
     * 分机绑定员工
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bind(Request $request)
    {
        $data = $request->all(['sip_id','staff_id']);
        //验证分机号
        $sip = Sip::where('id',$data['sip_id'])->where('merchant_id',$request->user()->merchant_id)->first();
        if ($sip == null){
            return Response::json(['code'=>1,'msg'=>'分机号不存在']);
        }
        if ($sip->staff_id){
            return Response::json(['code'=>1,'msg'=>'该分机号已经绑定了员工，请先解绑']);
        }
        //验证员工
        $staff = Staff::where('id',$data['staff_id'])->where('merchant_id',$request->user()->merchant_id)->first();
        if ($staff == null){
            return Response::json(['code'=>1,'msg'=>'员工不存在']);
        }
        try {
            $sip->update([
                'staff_id' => $data['staff_id'],
                'bind_time' => date('Y-m-d H:i:s'),
            ]);
            return Response::json(['code'=>0,'msg'=>'绑定成功','refresh'=>true]);
        }catch (\Exception $exception){
            Log::error('分机绑定员工异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'绑定失败']);
        }
    }

    /**
     * 分机解除绑定员工
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unbind(Request $request)
    {
        $data = $request->all(['sip_id']);
        //验证分机号
        $sip = Sip::where('id',$data['sip_id'])->where('merchant_id',$request->user()->merchant_id)->first();
        if ($sip == null){
            return Response::json(['code'=>1,'msg'=>'分机号不存在']);
        }
        try {
            $sip->update([
                'staff_id' => 0,
                'bind_time' => null,
            ]);
            return Response::json(['code'=>0,'msg'=>'解绑成功']);
        }catch (\Exception $exception){
            Log::error('分机绑定员工异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'解绑失败']);
        }
    }

    /**
     * 我的分机
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function mine(Request $request)
    {
        $sip = Sip::with('freeswitch')
            ->where('merchant_id',$request->user()->merchant_id)
            ->where('staff_id',$request->user()->id)
            ->first();
        if ($sip == null){
            abort(404,'您未绑定分机');
        }
        if ($request->ajax()){
            $data = $request->all(['password']);
            try {
                $sip->update($data);
                return Response::json(['code'=>0,'msg'=>'更新成功']);
            }catch (\Exception $exception){
                Log::error('前台更新分机信息异常：'.$exception->getMessage(),$data);
                return Response::json(['code'=>1,'msg'=>'更新失败']);
            }
        }
        return View::make('frontend.call.sip.mine',compact('sip'));
    }

}
