<?php

namespace App\Http\Controllers\Admin;

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
        $search = $request->all(['caller_id_number','destination_number','start_stamp_start','start_stamp_end']);
        if ($search['caller_id_number']){
            $query = $query->where('caller_id_number',$search['caller_id_number']);
        }
        if ($search['destination_number']){
            $query = $query->where('destination_number',$search['destination_number']);
        }
        if ($search['start_stamp_start'] && !$search['start_stamp_end']){
            $query = $query->where('start_stamp','>=',$search['start_stamp_start']);
        }else if (!$search['start_stamp_start'] && $search['start_stamp_end']){
            $query = $query->where('start_stamp','<=',$search['start_stamp_end']);
        }else if ($search['start_stamp_start'] && $search['start_stamp_end']){
            $query = $query->whereBetween('start_stamp',[$search['start_stamp_start'],$search['start_stamp_end']]);
        }
        $res = $query->orderBy('created_at')->paginate($request->get('limit', 30));
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
        //
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
        $cdr = Cdr::where(['aleg_uuid'=>$uuid])->first();
        if ($cdr==null){
            return ['code'=>'1','msg'=>'通话记录不存在'];
        }
        if (empty($cdr->sofia_record_file)){
            return ['code'=>'1','msg'=>'未找到录音文件'];
        }
        return ['code'=>0,'msg'=>'请求成功','data'=>$cdr->sofia_record_file];
    }

    /**
     * 下载录音
     * @param $uuid
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($uuid)
    {
        $cdr = Cdr::where(['aleg_uuid'=>$uuid])->first();
        if ($cdr==null){
            return back()->withErrors(['error'=>'通话记录不存在']);
        }
        if (!file_exists($cdr->sofia_record_file)){
            return back()->withErrors(['error'=>'未找到录音文件']);
        }
        return response()->download($cdr->sofia_record_file,$uuid.".wav");
    }


}
