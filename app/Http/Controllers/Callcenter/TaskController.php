<?php

namespace App\Http\Controllers\Callcenter;

use App\Http\Controllers\Controller;
use App\Imports\CallImport;
use App\Models\Call;
use App\Models\Gateway;
use App\Models\Queue;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;

class TaskController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Task::query()->withCount('calls')->orderByDesc('id')->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('callcenter.task.index');
    }

    public function create()
    {
        $queues = Queue::query()->orderByDesc('id')->get();
        $gateways = Gateway::query()->orderByDesc('id')->get();
        return View::make('callcenter.task.create',compact('queues','gateways'));
    }

    public function store(Request $request)
    {
        $data = $request->all([
            'name',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'gateway_id',
            'queue_id',
            'max_channel',
        ]);
        try {
            Task::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加任务异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = Task::query()->findOrFail($id);
        $queues = Queue::query()->orderByDesc('id')->get();
        $gateways = Gateway::query()->orderByDesc('id')->get();
        return View::make('callcenter.task.edit',compact('model', 'queues', 'gateways'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all([
            'name',
            'date_start',
            'date_end',
            'time_start',
            'time_end',
            'gateway_id',
            'queue_id',
            'max_channel',
        ]);
        $model = Task::query()->findOrFail($id);
        try {
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新任务异常：'.$exception->getMessage());
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
        try {
            Task::destroy($ids);
            Call::query()->whereIn('task_id',$ids)->delete();
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除群呼任务异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function show(Request $request,$id)
    {
        $task = Task::query()->withCount(['calls','hasCalls','missCalls','successCalls','failCalls'])->findOrFail($id);
        $percent = $task->calls_count>0?100*round(($task->has_calls_count)/($task->calls_count),4).'%':'0.00%';
        if ($request->isMethod('post')){
            $tiers = DB::table('queue_agent')->where('queue_id',$task->queue_id)->pluck('agent_id');
            return response()->json(['code'=>0, 'msg'=>'请求成功']);
        }
        return view('callcenter.task.show',compact('task','percent'));
    }

    public function setStatus(Request $request)
    {
        $ids = $request->get('ids',[]);
        if (count($ids)!=1){
            return $this->error('请选择一条记录');
        }
        $task = Task::query()->withCount('calls')->find($ids[0]);
        if ($task==null){
            return $this->error('任务不存在');
        }
        if ($task->status==3){
            return $this->error('任务已完成，禁止操作');
        }
        $status = $request->get('status',1);

        if ($status==2&&$task->calls_count==0){
            return $this->error('任务未导入号码，禁止操作');
        }
        if ($status==1&&$task->status!=2){
            return $this->error('任务未启动，禁止操作');
        }
        try {
            $task->update(['status'=>$status]);
            $key = config('freeswitch.redis_key.callcenter_task');
            Redis::rPush($key,$task->id);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('设置任务状态异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function importCall(Request $request, $id)
    {
        $model = Task::query()->findOrFail($id);
        if ($request->ajax()){
            $file = $request->input('file');
            if ($file == null){
                return $this->error('请先上传文件');
            }
            $xlsFile = public_path().$file;
            try{
                Excel::import(new CallImport($id), $xlsFile);
                return $this->success('导入成功');
            }catch (\Exception $exception){
                Log::error('导入失败：'.$exception->getMessage());
                return $this->error('导入失败');
            }
        }
        return View::make('callcenter.task.import',compact('model'));
    }

    public function calls(Request $request)
    {
        $data = $request->all(['task_id','phone']);
        $res = Call::query()
            ->when($data['phone'],function ($q) use($data){
                return $q->where('phone','like','%'.$data['phone'].'%');
            })
            ->where('task_id',$data['task_id'])
            ->orderBy('id','asc')
            ->paginate($request->get('limit', 30));
        foreach ($res->items() as $item){
            $item->status_name = Arr::get(config('freeswitch.callcenter_call_status'),$item->status,'-');
        }
        return $this->success('ok',$res->items(),$res->total());
    }

}
