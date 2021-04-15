<?php

namespace App\Http\Controllers\Home;

use App\Models\Asr;
use App\Models\Cdr;
use App\Models\Sip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CdrController extends Controller
{

    public function index()
    {
        return view('home.cdr.index');
    }

    public function data(Request $request)
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        $usernames = Sip::where('merchant_id',$merchant_id)->pluck('username');
        $query = Cdr::query()->with('bleg');
        $search = $request->all(['src','dst','start_at_start','start_at_end']);
        if ($search['src']){
            $query = $query->where('caller_id_number',$search['src']);
        }
        if ($search['dst']){
            $query = $query->where('destination_number',$search['dst']);
        }
        if ($search['start_at_start'] && !$search['start_at_end']){
            $query = $query->where('start_stamp','>=',$search['start_at_start']);
        }else if (!$search['start_at_start'] && $search['start_at_end']){
            $query = $query->where('start_stamp','<=',$search['start_at_end']);
        }else if ($search['start_at_start'] && $search['start_at_end']){
            $query = $query->whereBetween('start_stamp',[$search['start_at_start'],$search['start_at_end']]);
        }
        $res = $query->whereIn('caller_id_number',$usernames)->orderByDesc('id')->paginate($request->get('limit', 30));
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


}
