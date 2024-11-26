<?php

namespace App\Http\Controllers\Call;


use App\Models\Extension;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ExtensionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()){
            $query = Extension::query();
            $res = $query->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('call.dialplan.extension.index');
    }

    public function create()
    {
        return View::make('call.dialplan.extension.create');
    }


    public function store(Request $request)
    {
        $data = $request->all(['display_name','name','sort','continue','context']);
        try {
            Extension::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加拨号计划异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function show($id)
    {
        $extension = Extension::with('conditions')->findOrFail($id);
        return View::make('call.dialplan.extension.show',compact('extension'));
    }


    public function edit($id)
    {
        $model = Extension::findOrFail($id);
        return View::make('call.dialplan.extension.edit',compact('model'));
    }


    public function update(Request $request, $id)
    {
        $model = Extension::findOrFail($id);
        $data = $request->all(['display_name','name','sort','continue','context']);
        try {
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新拨号计划异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try {
            Extension::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除拨号计划异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function updateXml()
    {
        $extension = DB::table('extension')->orderBy('sort')->get()->groupBy('context')->toArray();
        $condition = DB::table('condition')->orderBy('sort')->get()->groupBy('extension_id')->toArray();
        $action = DB::table('action')->orderBy('sort')->get()->groupBy('condition_id')->toArray();
        if (empty($extension)){
            return $this->error();
        }
        foreach ($condition as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $value2->action = isset($action[$value2->id]) ? $action[$value2->id] : [];
            }
        }

        foreach ($extension as $key1 => $value1) {
            foreach ($value1 as $key2 => $value2) {
                $value2->condition = isset($condition[$value2->id]) ? $condition[$value2->id] : [];
            }
        }
        $data = $extension;
        try{
            $client = new Client();
            $client->post(config('freeswitch.swoole_http_url.dialplan'),
                [
                    'json'=>$data,
                    'timeout' => 30,
                ]
            );
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新拨号计划异常：' . $exception->getMessage());
            return $this->error();
        }
    }

}
