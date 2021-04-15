<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GatewayRequest;
use App\Models\Gateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;

class GatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.gateway.index');
    }

    public function data(Request $request)
    {
        $res = Gateway::orderByDesc('id')->paginate($request->get('limit', 30));
        foreach ($res->items() as $d){
            $d->status = Gateway::getStatus($d);
        }

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
        return view('admin.gateway.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GatewayRequest $request)
    {
        $data = $request->except(['_method','_token']);
        if (Gateway::create($data)){
            return redirect(route('admin.gateway'))->with(['success'=>'添加成功']);
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
        $model = Gateway::findOrFail($id);
        return view('admin.gateway.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(GatewayRequest $request, $id)
    {
        $model = Gateway::findOrFail($id);
        $data = $request->except(['_method','_token']);
        if ($model->update($data)){
            return redirect(route('admin.gateway'))->with(['success'=>'更新成功']);
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
        if (Gateway::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function updateXml()
    {
        set_time_limit(0);
        $gateway = Gateway::get()->toArray();
        if (empty($gateway)){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            $client = new Client();
            $res = $client->post(config('freeswitch.swoole_http_url.gateway'),['form_params'=>['data'=>$gateway]]);
            return $res->getBody();
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }
    }

}
