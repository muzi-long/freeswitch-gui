<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\Task\TaskRequest;
use App\Models\Agent;
use App\Models\Call;
use App\Models\Gateway;
use App\Models\Queue;
use App\Models\Task;
use Carbon\Carbon;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Log;
use Illuminate\Support\Facades\Redis;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Task::orderByDesc('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        return view('admin.task.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $queues = Queue::orderByDesc('id')->get();
        $gateways = Gateway::orderByDesc('id')->get();
        return view('admin.task.create',compact('queues','gateways'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $data = $request->except(['_method','_token']);
        if (Task::create($data)){
            return redirect(route('admin.task'))->with(['success'=>'添加成功']);
        }
        return back()->withErrors(['error'=>'添加失败']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $task = Task::withCount(['calls','hasCalls','missCalls','successCalls','failCalls'])->findOrFail($id);
        $percent = $task->calls_count>0?100*round(($task->has_calls_count)/($task->calls_count),4):'0.00%';
        if ($request->isMethod('post')){
            $tiers = DB::table('queue_agent')->where('queue_id',$task->queue_id)->pluck('agent_id');
            $agents = Agent::whereIn('id',$tiers)->get();
            return response()->json(['code'=>0, 'msg'=>'请求成功', 'data'=>$agents]);
        }
        return view('admin.task.show',compact('task','percent'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Task::findOrFail($id);
        $queues = Queue::orderByDesc('id')->get();
        $gateways = Gateway::orderByDesc('id')->get();
        return view('admin.task.edit',compact('model', 'queues', 'gateways'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, $id)
    {
        $data = $request->except(['_method','_token']);
        $model = Task::findOrFail($id);
        if ($model->update($data)){
            return redirect(route('admin.task'))->with(['success'=>'更新成功']);
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
        if (Task::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function setStatus(Request $request)
    {
        $ids = $request->get('ids',[]);
        if (count($ids)!=1){
            return response()->json(['code'=>1,'msg'=>'请选择一条记录']);
        }
        $task = Task::withCount('calls')->find($ids[0]);
        if ($task==null){
            return response()->json(['code'=>1,'msg'=>'任务不存在']);
        }
        if ($task->status==3){
            return response()->json(['code'=>1,'msg'=>'任务已完成，禁止操作']);
        }
        $status = $request->get('status',1);

        if ($status==2&&$task->calls_count==0){
            return response()->json(['code'=>1,'msg'=>'任务未导入号码，禁止操作']);
        }
        if ($status==1&&$task->status!=2){
            $key = config('freeswitch.redis_key.callcenter_task');
            Redis::rPush($key,$task->id);
            return response()->json(['code'=>1,'msg'=>'任务未启动，禁止操作']);
        }

        if ($task->update(['status'=>$status])){
            return response()->json(['code'=>0,'msg'=>'更新成功']);
        }
        return response()->json(['code'=>1,'msg'=>'更新失败']);
    }

    public function importCall(Request $request, $id)
    {
        set_time_limit(0);
        $task = Task::find($id);
        if ($task==null){
            return response()->json(['code'=>1,'msg'=>'任务不存在']);
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
                $disk = Storage::disk('uploads');
                $disk->put($newFile,file_get_contents($file->getRealPath()));
                $url = public_path('uploads').'/'.$newFile;
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
                        DB::table('call')->insert([
                            'task_id'   => $task->id,
                            'phone'     => $d,
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

    /**
     * 呼叫详情
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calls(Request $request)
    {
        $data = $request->all(['task_id','phone']);
        $res = Call::with('agent')
            ->when($data['phone'],function ($q) use($data){
                return $q->where('phone','like','%'.$data['phone'].'%');
            })->where('task_id',$data['task_id'])
            ->orderBy('id','asc')
            ->paginate($request->get('limit', 30));
        foreach ($res->items() as $item){
            $item->status_name = Arr::get(config('freeswitch.callcenter_call_status'),$item->status,'-');
        }
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

}
