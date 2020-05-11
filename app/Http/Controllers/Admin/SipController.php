<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Sip\SipListRequest;
use App\Http\Requests\Sip\SipRequest;
use App\Models\Sip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use GuzzleHttp\Client;
use App\Models\Gateway;

class SipController extends Controller
{
    /**
     * 分机列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $query = Sip::query();
            $username = $request->get('username');
            if ($username){
                $query = $query->where('username',$username);
            }
            $res = $query->orderByDesc('id')->paginate($request->get('limit', 30));
            foreach ($res->items() as $d){
                $d->status = null;
            }
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        return view('admin.sip.index');
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gateways = Gateway::select(['id','name'])->get();
        return view('admin.sip.create',compact('gateways'));
    }

    /**
     * @param SipRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(SipRequest $request)
    {
        $data = $request->all([
            'gateway_id',
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        try{
            Sip::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::error('添加分机异常：'.$exception->getMessage());
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
        $model = Sip::findOrFail($id);
        $gateways = Gateway::select(['id','name'])->get();
        return view('admin.sip.edit',compact('model','gateways'));
    }

    /**
     * @param SipRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(SipRequest $request, $id)
    {
        $model = Sip::findOrFail($id);
        $data = $request->all([
            'gateway_id',
            'username',
            'password',
            'effective_caller_id_name',
            'effective_caller_id_number',
            'outbound_caller_id_name',
            'outbound_caller_id_number',
        ]);
        if ($data['effective_caller_id_name']==null){
            $data['effective_caller_id_name'] = $data['username'];
        }
        if ($data['effective_caller_id_number']==null){
            $data['effective_caller_id_number'] = $data['username'];
        }
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('更新分机异常：'.$exception->getMessage());
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
        if (Sip::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function createList()
    {
        $gateways = Gateway::select(['id','name'])->get();
        return view('admin.sip.create_list',compact('gateways'));
    }

    public function storeList(SipListRequest $request)
    {
        $data = $request->all(['sip_start','sip_end','password','gateway_id']);
        if ($data['sip_start'] <= $data['sip_end']){
            //开启事务
            DB::beginTransaction();
            try{
                for ($i=$data['sip_start'];$i<=$data['sip_end'];$i++){
                    DB::table('sip')->insert([
                        'gateway_id' => $data['gateway_id'],
                        'username'  => $i,
                        'password'  => $data['password'],
                        'effective_caller_id_name' => $i,
                        'effective_caller_id_number' => $i,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                DB::commit();
                return Response::json(['code'=>0,'msg'=>'添加成功']);
            }catch (\Exception $exception) {
                DB::rollback();
                Log::error('批量添加分机异常：'.$exception->getMessage());
                return Response::json(['code'=>1,'msg'=>'添加失败']);
            }
        }
        return Response::json(['code'=>1,'msg'=>'开始分机号必须小于等于结束分机号']);
    }

    /**
     * 更新配置
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateXml(){
        $sips = DB::table('sip')->get()->toArray();
        if (empty($sips)){
            return response()->json(['code'=>1,'msg'=>'无数据需要更新']);
        }
        try{
            $client = new Client();
            $res = $client->post(config('swoole_http_url.directory'),['form_params'=>['data'=>json_encode($sips)],'timeout'=>30]);
            return response()->json(json_decode($res->getBody(),true));
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$exception->getMessage()]);
        }   
    }

    /**
     * 切换网关
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateGatewayForm(){
        $gateways = Gateway::get();
        return view('admin.sip.update_gateway',compact('gateways'));
    }

    public function updateGateway(Request $request){
        $data = $request->all(['gateway_id','content']);
        if (preg_match('/(\d{4,5})-(\d{4,5})/', $data['content'],$arr)) { //区间
            if ((int)$arr[1] <= (int)$arr[2]) {
                try{
                    Sip::where('username','>=',$arr[1])->where('username','<=',$arr[2])->update(['gateway_id'=>$data['gateway_id']]);
                    return response()->json(['code'=>0,'msg'=>'更新成功']);
                }catch(\Exception $e){
                    return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$e->getMessage()]);
                }
            }else{
                return response()->json(['code'=>1,'msg'=>'参数不合法']);
            }
        }elseif(strpos($data['content'], ",")!==false){ //多个
            $arr = explode(",",$data['content']);
            try{
                Sip::whereIn('username',$arr)->update(['gateway_id'=>$data['gateway_id']]);
                return response()->json(['code'=>0,'msg'=>'更新成功']);
            }catch(\Exception $e){
                return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$e->getMessage()]);
            }
        }else{ //单个
            try{
                Sip::where('username',$data['content'])->update(['gateway_id'=>$data['gateway_id']]);
                return response()->json(['code'=>0,'msg'=>'更新成功']);
            }catch(\Exception $e){
                return response()->json(['code'=>1,'msg'=>'更新失败','data'=>$e->getMessage()]);
            }
        }
    }
    
}
