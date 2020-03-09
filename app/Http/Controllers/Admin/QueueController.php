<?php

namespace App\Http\Controllers\Admin\pbx;

use App\Http\Requests\QueueRequest;
use App\Models\Agent;
use App\Models\Queue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

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
        $display_name = $request->get('display_name');
        $res = Queue::withCount(['agents'])->when($display_name,function($q) use($display_name){
            return $q->where('display_name','like','%'.$display_name.'%');
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
        $queues = Queue::with('agents')->get();
        if ($queues->isEmpty()){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        $agents = Agent::get();
        try{
            $client = new Client();
            $response = $client->post(config('freeswitch.swoole_http_url.callcenter'),[
                'form_params'=>[
                    'data'=>['queues'=>$queues->toArray(),'agents'=>$agents->toArray()]
                ],
                'timeout'=>30
            ]);
            if ($response->getStatusCode()==200) {
                $res = json_decode($response->getBody(),true);
                return response()->json($res);
            }
            return response()->json(['code'=>1,'msg'=>'更新失败']);
            
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
