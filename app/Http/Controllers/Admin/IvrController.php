<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\IvrRequest;
use App\Models\Digits;
use App\Models\Ivr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class IvrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.ivr.index');
    }

    public function data(Request $request)
    {
        $res = Ivr::orderByDesc('id')->paginate($request->get('limit', 30));
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
        return view('admin.ivr.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IvrRequest $request)
    {
        $data = $request->all();
        try{
            Ivr::create($data);
            return redirect()->to(route('admin.ivr'))->with(['success'=>'添加成功']);
        }catch (\Exception $exception){
            return back()->withInput()->withErrors('添加失败：'.$exception->getMessage());
        }
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
        $model = Ivr::findOrFail($id);
        return view('admin.ivr.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IvrRequest $request, $id)
    {
        $model = Ivr::findOrFail($id);
        $data = $request->all();
        try{
            $model->update($data);
            return redirect()->to(route('admin.ivr'))->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            return back()->withInput()->withErrors('更新失败：'.$exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $ivr = Ivr::whereIn('id',$ids)->first();
        if ($ivr==null){
            return response()->json(['code'=>1,'msg'=>'记录不存在']);
        }
        $digits = Digits::where('ivr_id',$ivr->id)->orWhere(function ($query) use ($ivr) {
            $query->where('action','menu-sub')->where('param',$ivr->name);
        })->count();
        if ($digits){
            return response()->json(['code'=>1,'msg'=>'IVR被使用，禁止删除']);
        }
        try{
            $ivr->delete();
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'删除失败：'.$exception->getMessage()]);
        }
    }

    public function updateXml()
    {
        set_time_limit(0);
        $datas = Ivr::with('digits')->get();
        if ($datas->isEmpty()){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            foreach ($datas as $data){
                $xml  ="<include>\n";
                $xml .="<menu name=\"".$data->name."\"\n";
                $xml .="greet-long=\"".$data->greet_long."\"\n";
                $xml .="greet-short=\"".$data->greet_short."\"\n";
                $xml .="invalid-sound=\"".$data->invalid_sound."\"\n";
                $xml .="exit-sound=\"".$data->exit_sound."\"\n";
                $xml .="confirm-macro=\"".$data->confirm_macro."\"\n";
                $xml .="confirm-key=\"".$data->confirm_key."\"\n";
                $xml .="tts-engine=\"".$data->tts_engine."\"\n";
                $xml .="tts-voice=\"".$data->tts_voice."\"\n";
                $xml .="confirm-attempts=\"".$data->confirm_attempts."\"\n";
                $xml .="timeout=\"".$data->timeout."\"\n";
                $xml .="inter-digit-timeout=\"".$data->inter_digit_timeout."\"\n";
                $xml .="max-failures=\"".$data->max_failures."\"\n";
                $xml .="max-timeouts=\"".$data->max_timeout."\"\n";
                $xml .="digit-len=\"".$data->digit_len."\">\n";

                if ($data->digits->isNotEmpty()){
                    foreach ($data->digits as $digit){
                        $xml .="<entry action=\"".$digit->action."\" digits=\"".$digit->digit."\" param=\"".$digit->param."\"/>\n";
                    }
                }

                $xml .="</menu>\n";
                $xml .="</include>\n";
                file_put_contents(config('freeswitch.ivr_dir').$data->name.".xml",$xml);
            }
            //生产环境，并且debug关闭的情况下自动更新网关注册信息
            if (config('app.env')=='production' && config('app.debug')==false){
                $freeswitch = new \Freeswitchesl();
                if (!$freeswitch->connect(config('freeswitch.event_socket.host'), config('freeswitch.event_socket.port'), config('freeswitch.event_socket.password'))){
                    return response()->json(['code'=>1,'msg'=>'ESL未连接']);
                }
                $freeswitch->bgapi("reloadxml");
                $freeswitch->disconnect();
                return response()->json(['code'=>0,'msg'=>'更新成功']);
            }
            return response()->json(['code'=>1,'msg'=>'请在生产环境下更新配置']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }
    }
}
