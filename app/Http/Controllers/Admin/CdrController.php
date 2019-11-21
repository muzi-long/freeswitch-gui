<?php

namespace App\Http\Controllers\Admin;

use App\Models\Asr;
use App\Models\Cdr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CdrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            $query = $query->where('start_at','>=',$search['start_at_start']);
        }else if (!$search['start_at_start'] && $search['start_at_end']){
            $query = $query->where('start_at','<=',$search['start_at_end']);
        }else if ($search['start_at_start'] && $search['start_at_end']){
            $query = $query->whereBetween('start_at',[$search['start_at_start'],$search['start_at_end']]);
        }
        $res = $query->orderByDesc('id')->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cdr = Cdr::find($id);
        $record = [];
        if ($cdr!=null){
            $record = Asr::whereIn('uuid',[$cdr->uuid])->orderByDesc('id')->get();
        }
        return view('admin.cdr.asr',compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
