<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gateway;
use App\Models\GatewayOutbound;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Provider\Uuid;

class GatewayOutboundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gateways = Gateway::get();
        return view('admin.gateway_outbound.index',compact('gateways'));
    }

    public function data(Request $request)
    {
        $data = $request->all(['gateway_id','number']);
        $res = GatewayOutbound::with('gateway')
        ->when($data['gateway_id'],function($q) use($data){
            return $q->where('gateway_id',$data['gateway_id']);
        })
        ->when($data['number'],function($q) use($data){
            return $q->where('number',$data['number']);
        })
        ->orderByDesc('id')->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return Response::json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gateways = Gateway::get();
        return View::make('admin.gateway_outbound.create',compact('gateways'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all(['gateway_id','number','status']);
        try{
            GatewayOutbound::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::info('添加网关号码异常：'.$exception->getMessage(),$data);
            return Response::json(['code'=>1,'msg'=>'添加失败']);
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
        $model = GatewayOutbound::findOrFail($id);
        $gateways = Gateway::get();
        return View::make('admin.gateway_outbound.edit',compact('model','gateways'));
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
        $model = GatewayOutbound::where('id',$id)->first();
        if ($model == null){
            return Response::json(['code'=>1,'msg'=>'词库不存在']);
        }
        $data = $request->all(['gateway_id','number','status']);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::info('更新网关号码异常：'.$exception->getMessage(),$data);
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
        if (GatewayOutbound::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }


    public function importForm()
    {
        $gateways = Gateway::get();
        return View::make('admin.gateway_outbound.import',compact('gateways'));
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        $gateway_id = $request->get('gateway_id');

        $gateway = Gateway::find($gateway_id);
        if ($gateway==null){
            return response()->json(['code'=>1,'msg'=>'网关不存在']);
        }
        $file = $request->file('file');
        if ($file->isValid()){
            $allowed_extensions = ['csv'];
            //上传文件最大大小,单位M  500Kb大约4万条数据
            $maxSize = 1;
            //检测类型
            $ext = $file->getClientOriginalExtension();
            if (!in_array(strtolower($ext),$allowed_extensions)){
                return response()->json(['code'=>1,'msg'=>"请上传".implode(",",$allowed_extensions)."格式"]);
            }
            //检测大小
            if ($file->getClientSize() > $maxSize*1024*1024){
                return response()->json(['code'=>1,'msg'=>"图片大小限制".$maxSize."M"]);
            }
            //上传到七牛云
            $newFile = Uuid::uuid().".".$file->getClientOriginalExtension();
            try{
                Storage::disk('uploadfile')->put($newFile,file_get_contents($file->getRealPath()));
                $url = public_path('uploadfile').'/'.$newFile;
            }catch (\Exception $exception){
                return response()->json(['code'=>1,'msg'=>'文件上传失败','data'=>$exception->getMessage()]);
            }
            //文件内容读取
            $data = [];
            try{
                $fp = fopen($url,"r");
                while(!feof($fp))
                {
                    $line = fgetcsv($fp);
                    if ($line){
                        foreach ($line as $phone){
                            array_push($data,$phone);
                        }
                    }
                }
                fclose($fp);
                //去重,去空
                $data = array_filter(array_unique($data));
            }catch (\Exception $exception){
                return response()->json(['code'=>1,'msg'=>'读取文件内容错误','data'=>$exception->getMessage()]);
            }

            //写入数据库
            if (!empty($data)){
                DB::beginTransaction();
                try{
                    foreach ($data as $d){
                        DB::table('gateway_outbound')->insert([
                            'gateway_id'   => $gateway->id,
                            'number'     => $d,
                            'status' => 1,
                            'created_at'=> Carbon::now(),
                            'updated_at'=> Carbon::now(),
                        ]);
                    }
                    DB::commit();
                    return response()->json(['code'=>0,'msg'=>'导入完成']);
                }catch (\Exception $exception){
                    DB::rollBack();
                    return response()->json(['code'=>1,'msg'=>'导入失败','data'=>$exception->getMessage()]);
                }
            }
            return response()->json(['code'=>1,'msg'=>'导入数据为空']);
        }
        return response()->json(['code'=>1,'msg'=>'上传失败','data'=>$file->getErrorMessage()]);
    }

}
