<?php

namespace App\Http\Controllers\Admin\pbx;

use App\Http\Requests\AgentRequest;
use App\Models\Agent;
use App\Models\Sip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return view('admin.agent.index');
    }

    public function data(Request $request)
    {
        $data = $request->all(['display_name','originate_number']);
        $res = Agent::when($data['display_name'],function($q) use($data){
            return $q->where('display_name','like','%'.$data['display_name'].'%');
        })->when($data['originate_number'],function($q) use($data){
            return $q->where('originate_number','like','%'.$data['originate_number'].'%');
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.agent.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgentRequest $request)
    {
        $data = $request->all();
        if (Agent::create($data)){
            return redirect(route('admin.agent'))->with(['success'=>'添加成功']);
        }
        return back()->withErrors(['error'=>'添加失败']);
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
        $model = Agent::findOrFail($id);
        return view('admin.agent.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AgentRequest $request, $id)
    {
        $model = Agent::findOrFail($id);
        $data = $request->except(['_method','_token']);
        if ($model->update($data)){
            return redirect(route('admin.agent'))->with(['success'=>'更新成功']);
        }
        return back()->withErrors(['error'=>'更新失败']);
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
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Agent::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }


    public function check(Request $request){
        $data = $request->all(['ids','status']);
        if (empty($data['ids']) || !is_array($data['ids'])) {
            return response()->json(['code'=>1,'msg'=>'请选择操作项']);
        }
        if (!in_array($data['status'], [0,1])) {
            return response()->json(['code'=>1,'msg'=>'状态参数错误']);
        }
        $status = $data['status']==1?'Available':'Logged Out';
        $freeswitch = new \Freeswitchesl();
        $service = config('freeswitch.service')[1];
        try{
            if ($freeswitch->connect($service['host'], $service['port'], $service['password'])) {
                foreach ($data['ids'] as $id) {
                    $freeswitch->api("callcenter_config agent set status agent".$id." '".$status."'");
                }
                $freeswitch->disconnect();
                return response()->json(['code'=>0,'msg'=>'更新成功']);
            }else{
                return response()->json(['code'=>1,'msg'=>'更新成功']);
            }    
        }catch(\Exception $e){
            return response()->json(['code'=>1,'msg'=>'系统错误','data'=>$e->getMessage()]);
        }

    }

}
