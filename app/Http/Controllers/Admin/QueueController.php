<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\QueueRequest;
use App\Models\Agent;
use App\Models\Queue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.queue.index');
    }

    public function data(Request $request)
    {
        $res = Queue::withCount(['agents'])->orderByDesc('id')->paginate($request->get('limit', 30));
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
        return view('admin.queue.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QueueRequest $request)
    {
        $data = $request->all();
        if (Queue::create($data)){
            return redirect(route('admin.queue'))->with(['success'=>'添加成功，请更新配置']);
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
        $model = Queue::findOrFail($id);
        return view('admin.queue.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(QueueRequest $request, $id)
    {
        $model = Queue::findOrFail($id);
        $data = $request->except(['_method','_token']);
        if ($model->update($data)){
            return redirect(route('admin.queue'))->with(['success'=>'更新成功，请更新配置']);
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
        if (Queue::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功,请更新配置']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);

    }

    public function updateXml()
    {
        $queues = Queue::with('agents')->whereHas('agents')->get();
        if ($queues->isEmpty()){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            //生产环境，并且debug关闭的情况下自动更新网关注册信息
            if (config('app.env')=='production' && config('app.debug')==false){
                $freeswitch = new \Freeswitchesl();
                if (!$freeswitch->connect(config('freeswitch.event_socket.host'), config('freeswitch.event_socket.port'), config('freeswitch.event_socket.password'))){
                    return response()->json(['code'=>1,'msg'=>'ESL未连接']);
                }
                $queues = Queue::with("agents")->get();
                $agents = Agent::get();

                $xml  = "<configuration name=\"callcenter.conf\" description=\"CallCenter\">\n";
                $xml .= "    <settings>\n";
                $xml .= "      <!--<param name=\"odbc-dsn\" value=\"dsn:user:pass\"/>-->\n";
                $xml .= "      <!--<param name=\"dbname\" value=\"/dev/shm/callcenter.db\"/>-->\n";
                $xml .= "      <!--<param name=\"cc-instance-id\" value=\"single_box\"/>-->\n";
                $xml .= "     <param name=\"truncate-tiers-on-load\" value=\"true\"/>\n";
                $xml .= "     <param name=\"truncate-agents-on-load\" value=\"true\"/>\n";
                $xml .= "    </settings>\n";
                //----------------------------------  写入队列信息 ------------------------------------
                $xml .= "<queues>\n";
                foreach ($queues as $queue){
                    $xml .= "    <queue name=\"".$queue->name."\">\n";
                    $xml .= "        <param name=\"strategy\" value=\"".$queue->strategy."\"/>\n";
                    $xml .= "        <param name=\"moh-sound\" value=\"".$queue->moh_sound."\"/>\n";
                    $xml .= "        <param name=\"record-template\" value=\"\$\${recordings_dir}/\${strftime(%Y)}/\${strftime(%m)}/\${strftime(%d)}/.\${destination_number}.\${caller_id_number}.\${uuid}.wav\"/>\n";
                    $xml .= "        <param name=\"time-base-score\" value=\"".$queue->time_base_score."\"/>\n";
                    $xml .= "        <param name=\"max-wait-time\" value=\"".$queue->max_wait_time."\"/>\n";
                    $xml .= "        <param name=\"max-wait-time-with-no-agent\" value=\"".$queue->max_wait_time_with_no_agent."\"/>\n";
                    $xml .= "        <param name=\"max-wait-time-with-no-agent-time-reached\" value=\"".$queue->max_wait_time_with_no_agent_time_reached."\"/>\n";
                    $xml .= "        <param name=\"tier-rules-apply\" value=\"".$queue->tier_rules_apply."\"/>\n";
                    $xml .= "        <param name=\"tier-rule-wait-second\" value=\"".$queue->tier_rule_wait_second."\"/>\n";
                    $xml .= "        <param name=\"tier-rule-wait-multiply-level\" value=\"".$queue->tier_rule_wait_multiply_level."\"/>\n";
                    $xml .= "        <param name=\"tier-rule-no-agent-no-wait\" value=\"".$queue->tier_rule_no_agent_no_wait."\"/>\n";
                    $xml .= "        <param name=\"discard-abandoned-after\" value=\"".$queue->discard_abandoned_after."\"/>\n";
                    $xml .= "        <param name=\"abandoned-resume-allowed\" value=\"".$queue->abandoned_resume_allowed."\"/>\n";
                    $xml .= "    </queue>\n";
                }
                $xml .= "</queues>\n";

                //----------------------------------  写入坐席信息 ------------------------------------
                $xml .= "<agents>\n";
                foreach ($agents as $agent){
                    $xml .= "<agent name=\"".$agent->name."\" type=\"".$agent->type."\" contact=\"[leg_timeout=10]".$agent->originate_type."/".$agent->originate_number."\" status=\"".$agent->status."\" max-no-answer=\"".$agent->max_no_answer."\" wrap-up-time=\"".$agent->wrap_up_time."\" reject-delay-time=\"".$agent->reject_delay_time."\" busy-delay-time=\"".$agent->busy_delay_time."\" no-answer-delay-time=\"".$agent->no_answer_delay_time."\" />\n";
                }
                $xml .= "</agents>\n";

                //----------------------------------  写入队列-坐席信息 ------------------------------------
                $xml .= "<tiers>\n";
                foreach ($queues as $queue){
                    foreach ($queue->agents as $agent){
                        $xml .= "<tier agent=\"".$agent->name."\" queue=\"".$queue->name."\" level=\"1\" position=\"1\"/>\n";
                    }
                }
                $xml .= "</tiers>\n";
                $xml .= "</configuration>\n";
                //生成配置文件
                file_put_contents(config('freeswitch.callcenter_dir'),$xml);
                $freeswitch->bgapi("reload mod_callcenter");
                $freeswitch->disconnect();
                return response()->json(['code'=>0,'msg'=>'更新成功']);
            }
            return response()->json(['code'=>1,'msg'=>'请在生产环境下更新配置']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }
    }

    public function agent($id)
    {
        $queue = Queue::with('agents')->findOrFail($id);
        $agents = Agent::orderByDesc('id')->get();
        return view('admin.queue.agent',compact('queue','agents'));
    }

    public function assignAgent(Request $request,$id)
    {
        $queue = Queue::with('agents')->findOrFail($id);
        $names = $request->get('agents',[]);
        if ($queue->agents()->sync($names)){
            return redirect(route('admin.queue'))->with(['success'=>'更新成功']);
        }
        return back()->withErrors(['error'=>'更新失败']);
    }

    
}
