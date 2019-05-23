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
        $res = Queue::withCount('agents')->orderByDesc('id')->paginate($request->get('limit', 30));
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
        DB::beginTransaction();
        try{
            DB::table('queue')->where('id',$model->id)->update($data);
            DB::table('tiers')->where('queue',$model->name)->update(['queue'=>$data['name']]);
            DB::commit();
            return redirect(route('admin.queue'))->with(['success'=>'更新成功，请更新配置']);
        }catch (\Exception $exception){
            DB::rollback();
            return back()->withErrors(['error'=>'更新失败']);
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
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        $queues = Queue::whereIn('id',$ids)->pluck('name');
        DB::beginTransaction();
        try{
            DB::table('tiers')->whereIn('queue',$queues)->delete();
            DB::table('queue')->whereIn('id',$ids)->delete();
            DB::commit();
            return response()->json(['code'=>0,'msg'=>'删除成功,请更新配置']);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code'=>1,'msg'=>'删除失败','data'=>$e->getMessage()]);
        }
    }

    public function updateXml()
    {
        $queues = Queue::get();
        if ($queues->isEmpty()){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            $xml  = "<configuration name=\"callcenter.conf\" description=\"CallCenter\">\n";
            $xml .= "    <settings>\n";
            $xml .= "        <param name=\"odbc-dsn\" value=\"freeswitch:root:pwd\"/>\n";
            $xml .= "        <!--<param name=\"dbname\" value=\"/dev/shm/callcenter.db\"/>-->\n";
            $xml .= "        <!--<param name=\"cc-instance-id\" value=\"single_box\"/>-->\n";
            $xml .= "    </settings>\n";
            $xml .= "    <queues>\n";
            foreach ($queues->toArray() as $q){
                $xml  .= "      <queue name=\"".$q['name']."\">\n";
                $xml  .= "          <param name=\"strategy\" value=\"".$q['strategy']."\"/>\n";
                $xml  .= "          <param name=\"moh-sound\" value=\"".$q['moh-sound']."\"/>\n";
                $xml  .= "          <!--<param name=\"record-template\" value=\"".$q['record-template']."\"/>-->\n";
                $xml  .= "          <param name=\"time-base-score\" value=\"".$q['time-base-score']."\"/>\n";
                $xml  .= "          <param name=\"max-wait-time\" value=\"".$q['max-wait-time']."\"/>\n";
                $xml  .= "          <param name=\"max-wait-time-with-no-agent\" value=\"".$q['max-wait-time-with-no-agent']."\"/>\n";
                $xml  .= "          <param name=\"max-wait-time-with-no-agent-time-reached\" value=\"".$q['max-wait-time-with-no-agent-time-reached']."\"/>\n";
                $xml  .= "          <param name=\"tier-rules-apply\" value=\"".$q['tier-rules-apply']."\"/>\n";
                $xml  .= "          <param name=\"tier-rule-wait-second\" value=\"".$q['tier-rule-wait-second']."\"/>\n";
                $xml  .= "          <param name=\"tier-rule-wait-multiply-level\" value=\"".$q['tier-rule-wait-multiply-level']."\"/>\n";
                $xml  .= "          <param name=\"tier-rule-no-agent-no-wait\" value=\"".$q['tier-rule-no-agent-no-wait']."\"/>\n";
                $xml  .= "          <param name=\"discard-abandoned-after\" value=\"".$q['discard-abandoned-after']."\"/>\n";
                $xml  .= "          <param name=\"abandoned-resume-allowed\" value=\"".$q['abandoned-resume-allowed']."\"/>\n";
                $xml .= "        </queue>\n";
            }
            $xml .= "    </queues>\n";
            $xml .= "    <agents>\n";
            $xml .= "    </agents>\n";
            $xml .= "    <tier>\n";
            $xml .= "    </tier>\n";
            $xml .= "</configuration>\n";
            file_put_contents(config('freeswitch.callcenter_dir'),$xml);
            //生产环境，并且debug关闭的情况下自动更新网关注册信息
            if (config('app.env')=='production' && config('app.debug')==false){
                $freeswitch = new \Freeswitchesl();
                $freeswitch->bgapi("reload callcenter");
            }
            return response()->json(['code'=>0,'msg'=>'更新成功']);
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
