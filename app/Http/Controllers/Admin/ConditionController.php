<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Dialplan\ConditionRequest;
use App\Models\Condition;
use App\Models\Extension;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConditionController extends Controller
{
    /**
     * 列表
     * @param Request $request
     * @param $extension_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request,$extension_id)
    {
        if ($request->ajax()){
            $res = Condition::where('extension_id',$extension_id)->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        $extension = Extension::findOrFail($extension_id);
        return view('admin.dialplan.condition.index',compact('extension'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($extension_id)
    {
        $extension = Extension::findOrFail($extension_id);
        return view('admin.dialplan.condition.create',compact('extension'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConditionRequest $request,$extension_id)
    {
        $extension = Extension::findOrFail($extension_id);
        $data = $request->all(['display_name','field','expression','break','sort']);
        $data['extension_id'] = $extension->id;
        if (Condition::create($data)){
            return redirect(route('admin.condition',['extension_id'=>$extension->id]))->with(['success'=>'添加成功']);
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
    public function edit($extension_id,$id)
    {
        $extension = Extension::findOrFail($extension_id);
        $model = Condition::findOrFail($id);
        return view('admin.dialplan.condition.edit',compact('extension','model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $extension_id, $id)
    {
        $extension = Extension::findOrFail($extension_id);
        $model = Condition::findOrFail($id);
        $data = $request->all(['display_name','field','expression','break','sort']);
        if ($model->update($data)){
            return redirect(route('admin.condition',['extension_id'=>$extension->id]))->with(['success'=>'更新成功']);
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
        if (Condition::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
