<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cdr;
use App\Models\Sip;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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


}
