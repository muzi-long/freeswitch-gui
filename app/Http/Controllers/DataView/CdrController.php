<?php

namespace App\Http\Controllers\DataView;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class CdrController extends Controller
{

    public function index(Request $request)
    {
        $users = User::query()->get();
        if ($request->ajax()){
            $res = $request->all(['user_id','created_at_start','created_at_end']);
            if ($res['user_id']){
                $data = $users->where('id',$res['user_id'])->keyBy('id')->all();
            }else{
                $data = $users->keyBy('id')->all();
            }

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
            Cdr::query()
                ->when($res['user_id'],function ($q) use($res){
                    return $q->where('user_id',$res['user_id']);
                })
                ->when($res['created_at_start'] && !$res['created_at_end'],function ($q) use($res){
                    return $q->where('created_at','>=',$res['created_at_start']);
                })
                ->when(!$res['created_at_start'] && $res['created_at_end'],function ($q) use($res){
                    return $q->where('created_at','<=',$res['created_at_end']);
                })
                ->when($res['created_at_start'] && $res['created_at_end'],function ($q) use($res){
                    return $q->whereBetween('created_at',[$res['created_at_start'],$res['created_at_end']]);
                })
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
            return $this->success('ok',$data,count($data));
        }
        return View::make('data_view.cdr.index',compact('users'));
    }

}
