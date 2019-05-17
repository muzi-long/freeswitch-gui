<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\QueueRequest;
use App\Models\Queue;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class QueueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.queue.index');
    }

    public function data(Request $request)
    {
        $res = Queue::orderByDesc('id')->paginate($request->get('limit', 30));
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
        return view('admin.queue.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QueueRequest $request)
    {
        $data = $request->all();
        if (Queue::create($data)){
            return redirect(route('admin.queue'))->with(['success'=>'添加成功']);
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
        $model = Queue::findOrFail($id);
        return view('admin.queue.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(QueueRequest $request, $id)
    {
        $model = Queue::findOrFail($id);
        $data = $request->all();
        if ($model->update($data)){
            return redirect(route('admin.queue'))->with(['success'=>'更新成功']);
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
        $queues = Queue::whereIn('id',$ids)->pluck('name');
        DB::beginTransaction();
        try{
            DB::table('tiers')->where('queue','in',$queues)->delete();
            DB::table('queue')->where('id','in',$ids)->delete();
            DB::commit();
            return response()->json(['code'=>0,'msg'=>'删除成功,请更新配置']);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code'=>1,'msg'=>'删除失败','data'=>$e->getMessage()]);
        }
    }

    public function updateXml()
    {
        
    }

    public function agent()
    {
        
    }

    public function assignAgent()
    {
        
    }
    
}
