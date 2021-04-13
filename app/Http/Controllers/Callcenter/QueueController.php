<?php

namespace App\Http\Controllers\Callcenter;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Models\Queue;

class QueueController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Queue::withCount('sips')->orderByDesc('id')->paginate($request->input('limit',30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('callcenter.queue.index');
    }

    public function create()
    {
        return View::make('callcenter.queue.create');
    }

    public function store(Request $request)
    {
        $data = $request->all([
            'name',
            'strategy',
            'max_wait_time',
            'sips',
        ]);
        $data['sips'] = $data['sips'] ? explode(',',$data['sips']) : [];
        DB::beginTransaction();
        try {
            $queueId = DB::table('queue')->insertGetId([
                'name' => $data['name'],
                'strategy' => $data['strategy'],
                'max_wait_time' => $data['max_wait_time'],
            ]);
            foreach ($data['sips'] as $sipId){
                DB::table('queue_sip')->insert([
                    'queue_id' => $queueId,
                    'sip_id' => $sipId,
                ]);
            }
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('添加队列异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = Queue::query()->where('id',$id)->first();
        return View::make('callcenter.queue.edit',compact('model'));
    }

    public function update(Request $request,$id)
    {
        $model = Queue::query()->where('id',$id)->first();
        $data = $request->all([
            'name',
            'strategy',
            'max_wait_time',
        ]);
        $sipids = $request->input('sips') ? explode(',',$request->input('sips')) : [];
        DB::beginTransaction();
        try {
            $model->update($data);
            $model->sips()->sync($sipids);
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('更新队列异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return $this->error('请选择删除项');
        }
        DB::beginTransaction();
        try{
            DB::table('queue_sip')->whereIn('queue_id',$ids)->delete();
            DB::table('queue')->whereIn('id',$ids)->delete();
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除队列异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function updateXml()
    {
        $queues = Queue::with('sips')->get()->toArray();
        try{
            $client = new Client();
            $client->post(config('freeswitch.swoole_http_url.callcenter'),
                [
                    'json' => $queues,
                    'timeout' => 30
                ]
            );
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新群呼配置异常：' . $exception->getMessage());
            return $this->error('更新失败');
        }
    }


}
