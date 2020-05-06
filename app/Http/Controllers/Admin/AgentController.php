<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Agent\AgentRequest;
use App\Models\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
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
        return view('admin.agent.index');
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
     * @param AgentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AgentRequest $request)
    {
        $data = $request->all();
        try{
            Agent::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::error('添加坐席异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
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
     * @param AgentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(AgentRequest $request, $id)
    {
        $model = Agent::findOrFail($id);
        $data = $request->except(['_method','_token']);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('更新坐席异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
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
        if (Agent::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    /**
     * 签入签出
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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
        $service = config('freeswitch.esl');
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
