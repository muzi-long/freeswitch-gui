<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cdr;
use App\Models\Sip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class CdrController extends Controller
{

    public function index()
    {
        return view('admin.cdr.index');
    }

    public function data(Request $request)
    {
        $query = Cdr::query();
        $search = $request->all(['src','dst','start_at_start','start_at_end']);
        if ($search['src']){
            $query = $query->where('src',$search['src']);
        }
        if ($search['dst']){
            $query = $query->where('dst',$search['dst']);
        }
        if ($search['start_at_start'] && !$search['start_at_end']){
            $query = $query->where('aleg_start_at','>=',$search['start_at_start']);
        }else if (!$search['start_at_start'] && $search['start_at_end']){
            $query = $query->where('aleg_start_at','<=',$search['start_at_end']);
        }else if ($search['start_at_start'] && $search['start_at_end']){
            $query = $query->whereBetween('aleg_start_at',[$search['start_at_start'],$search['start_at_end']]);
        }
        $user = $request->user();
        $res = $query->where(function ($query) use($user) {
            if ($user->hasPermissionTo('data.cdr.list_all')) {
                # code...
            }elseif ($user->hasPermissionTo('data.cdr.list_department')) {
                $user_ids = User::where('department_id',$user->department_id)->pluck('id')->toArray();
                return $query->whereIn('user_id',$user_ids);
            }else{
                return $query->where('user_id',$user->id);
            }
        })->orderByDesc('id')->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }


    /**
     * 播放录音
     * @param $uuid
     * @return array
     */
    public function play($uuid)
    {
        $cdr = Cdr::where('uuid',$uuid)->first();
        if ($cdr==null){
            return ['code'=>'1','msg'=>'通话记录不存在'];
        }
        if (empty($cdr->record_file)){
            return ['code'=>'1','msg'=>'未找到录音文件'];
        }
        return ['code'=>0,'msg'=>'请求成功','data'=>$cdr->record_file];
    }

    /**
     * 下载录音
     * @param $uuid
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($uuid)
    {
        $cdr = Cdr::where('uuid',$uuid)->first();
        if ($cdr==null){
            return back()->withErrors(['error'=>'通话记录不存在']);
        }
        if (!file_exists($cdr->record_file)){
            return back()->withErrors(['error'=>'未找到录音文件']);
        }
        return response()->download($cdr->record_file,$uuid.".wav");
    }

    /**
     * 人员统计
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function count(Request $request)
    {
        $users = User::where('id','!=',config('freeswitch.user_root_id'))->get();
        if ($request->ajax()){
            $res = $request->all(['user_id','start_stamp_start','start_stamp_end']);
            $data = $users->keyBy('id')->all();
            foreach ($data as &$d){
                $d['todayCalls'] = 0;
                $d['todaySuccessCalls'] = 0;
                $d['todayRateCalls'] = '0.00%';
                $d['todayThirtyCalls'] = 0;
                $d['todaySixtyCalls'] = 0;
                $d['weekCalls'] = 0;
                $d['weekSuccessCalls'] = 0;
                $d['weekRateCalls'] = '0.00%';
                $d['weekThirtyCalls'] = 0;
                $d['weekSixtyCalls'] = 0;
                $d['monthCalls'] = 0;
                $d['monthSuccessCalls'] = 0;
                $d['monthRateCalls'] = '0.00%';
                $d['monthThirtyCalls'] = 0;
                $d['monthSixtyCalls'] = 0;
            }
            Cdr::when($res['user_id'],function ($q) use($res){
                    return $q->where('user_id',$res['user_id']);
                })
                ->when($res['start_stamp_start'] && !$res['start_stamp_end'],function ($q) use($res){
                    return $q->where('aleg_start_at','>=',$res['start_stamp_start']);
                })
                ->when(!$res['start_stamp_start'] && $res['start_stamp_end'],function ($q) use($res){
                    return $q->where('aleg_start_at','<=',$res['start_stamp_end']);
                })
                ->when($res['start_stamp_start'] && $res['start_stamp_end'],function ($q) use($res){
                    return $q->whereBetween('aleg_start_at',[$res['start_stamp_start'],$res['start_stamp_end']]);
                })
                ->where('user_id','!=',config('freeswitch.user_root_id'))
                ->orderBy('id','asc')
                ->chunk(1000,function ($cdrs) use(&$data){
                    foreach ($cdrs as $cdr){
                        foreach ($data as $d){
                            if ($cdr->user_id==$d['id']){
                                $time = strtotime($cdr->aleg_start_at);
                                //当天
                                if ($time>=Carbon::today()->timestamp && $time<=Carbon::tomorrow()->timestamp){
                                    $d['todayCalls'] += 1;
                                }
                                if ($time>=Carbon::today()->timestamp && $time<=Carbon::tomorrow()->timestamp && $cdr->billsec>0){
                                    $d['todaySuccessCalls'] += 1;
                                }
                                if ($time>=Carbon::today()->timestamp && $time<=Carbon::tomorrow()->timestamp && $cdr->billsec>30){
                                    $d['todayThirtyCalls'] += 1;
                                }
                                if ($time>=Carbon::today()->timestamp && $time<=Carbon::tomorrow()->timestamp && $cdr->billsec>60){
                                    $d['todaySixtyCalls'] += 1;
                                }
                                //本周
                                if ($time>=Carbon::now()->startOfWeek()->timestamp && $time<=Carbon::now()->endOfWeek()->timestamp){
                                    $d['weekCalls'] += 1;
                                }
                                if ($time>=Carbon::now()->endOfWeek()->timestamp && $time<=Carbon::now()->endOfWeek()->timestamp && $cdr->billsec>0){
                                    $d['weekSuccessCalls'] += 1;
                                }
                                if ($time>=Carbon::now()->endOfWeek()->timestamp && $time<=Carbon::now()->endOfWeek()->timestamp && $cdr->billsec>30){
                                    $d['weekThirtyCalls'] += 1;
                                }
                                if ($time>=Carbon::now()->endOfWeek()->timestamp && $time<=Carbon::now()->endOfWeek()->timestamp && $cdr->billsec>60){
                                    $d['weekSixtyCalls'] += 1;
                                }
                                //本月
                                if ($time>=Carbon::now()->startOfMonth()->timestamp && $time<=Carbon::now()->endOfMonth()->timestamp){
                                    $d['monthCalls'] += 1;
                                }
                                if ($time>=Carbon::now()->startOfMonth()->timestamp && $time<=Carbon::now()->endOfMonth()->timestamp && $cdr->billsec>0){
                                    $d['monthSuccessCalls'] += 1;
                                }
                                if ($time>=Carbon::now()->startOfMonth()->timestamp && $time<=Carbon::now()->endOfMonth()->timestamp && $cdr->billsec>30){
                                    $d['monthThirtyCalls'] += 1;
                                }
                                if ($time>=Carbon::now()->startOfMonth()->timestamp && $time<=Carbon::now()->endOfMonth()->timestamp && $cdr->billsec>60){
                                    $d['monthSixtyCalls'] += 1;
                                }
                                break;
                            }
                        }
                    }
                });
            foreach ($data as &$d){
                $d['todayRateCalls'] = $d['todayCalls']>0?100*round($d['todaySuccessCalls']/$d['todayCalls'],4).'%':'0.00%';
                $d['weekRateCalls'] = $d['weekCalls']>0?100*round($d['weekSuccessCalls']/$d['weekCalls'],4).'%':'0.00%';
                $d['monthRateCalls'] = $d['monthCalls']>0?100*round($d['monthSuccessCalls']/$d['monthCalls'],4).'%':'0.00%';
            }
            return Response::json([
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => count($data),
                'data' => $data,
            ]);
        }

        return View::make('admin.cdr.count',compact('users'));
    }

}
