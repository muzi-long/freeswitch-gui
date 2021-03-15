<?php

namespace App\Http\Controllers;


use App\Models\Gateway;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class GatewayController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Gateway::query()->orderByDesc('id')->paginate($request->get('limit', 30));
            /*foreach ($res->items() as $d){
                $d->status = $d->getStatus($d->id);
            }*/
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('gateway.index');
    }


    public function create()
    {
        return View::make('gateway.create');
    }


    public function store(Request $request)
    {
        $data = $request->all(['name','realm','username','password','prefix','outbound_caller_id','type']);
        try{
            Gateway::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加网关异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function edit($id)
    {
        $model = Gateway::findOrFail($id);
        return View::make('gateway.edit',compact('model'));
    }


    public function update(Request $request, $id)
    {
        $model = Gateway::findOrFail($id);
        $data = $request->all(['name','realm','username','password','prefix','outbound_caller_id','type']);
        try{
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新网关异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return $this->error('请选择删除项');
        }
        try {
            Gateway::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除网关异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function updateXml()
    {
        $gateway = Gateway::query()->get()->toArray();
        try{
            $client = new Client();
            $client->post(config('freeswitch.swoole_http_url.gateway'),
                [
                    'json'=> $gateway,
                    'timeout' => 30,
                ]
            );
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新网关配置异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
