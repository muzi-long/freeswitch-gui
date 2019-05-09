<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GatewayRequest;
use App\Models\Gateway;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $data = $request->all(['name','realm','username','password']);
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
    public function update(Request $request, $id)
    {
        $model = Gateway::findOrFail($id);
        $data = $request->all(['name','realm','username','password']);
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
        $gateway = Gateway::get();
        if ($gateway->isEmpty()){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            foreach ($gateway as $gw){
                $xml  = "<include>\n";
                $xml .= "    <gateway name=\"gw".$gw->id."\">\n";
                $xml .= "       <param name=\"username\" value=\"".$gw->username."\"/>\n";
                $xml .= "       <param name=\"realm\" value=\"".$gw->realm."\"/>\n";
                $xml .= "       <param name=\"password\" value=\"".$gw->password."\"/>\n";
                $xml .= "    </gateway>\n";
                $xml .= "</include>";
                file_put_contents(config('freeswitch.gateway_dir')."gw".$gw->id.".xml",$xml);
            }
            //生产环境，并且debug关闭的情况下自动更新网关注册信息
            if (config('app.env')=='production' && config('app.debug')==false){
                $freeswitch = new \Freeswitchesl();
                $freeswitch->bgapi("sofia profile external rescan");
            }
            return response()->json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }
    }

}
