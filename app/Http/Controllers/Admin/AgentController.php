<?php

namespace App\Http\Controllers\Admin;

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
        $res = Agent::orderByDesc('id')->paginate($request->get('limit', 30));
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
        $sips = Sip::orderByDesc('id')->get();
        return view('admin.agent.create',compact('sips'));
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
        $data['contact'] = 'user/'.$data['contact'];
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
        $sips = Sip::orderByDesc('id')->get();
        return view('admin.agent.edit',compact('model','sips'));
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
        $data['contact'] = 'user/'.$data['contact'];
        DB::beginTransaction();
        try{
            DB::table('tiers')->where('agent',$model->name)->update(['agent'=>$data['name']]);
            DB::table('agents')->where('id',$model->id)->update($data);
            DB::commit();
            return redirect(route('admin.agent'))->with(['success'=>'更新成功']);
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
        $names = Agent::whereIn('id',$ids)->pluck('name');
        DB::beginTransaction();
        try{
            DB::table('tiers')->whereIn('agent',$names)->delete();
            DB::table('agents')->whereIn('id',$ids)->delete();
            DB::commit();
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code'=>1,'msg'=>'删除失败','data'=>$e->getMessage()]);
        }
    }
}
