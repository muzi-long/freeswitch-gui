<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Models\Sip;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.group.index');
    }

    public function data(Request $request)
    {
        $res = Group::orderByDesc('id')->paginate($request->get('limit', 30));
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
        return view('admin.group.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GroupRequest $request)
    {
        $data = $request->all(['display_name','name']);
        if (Group::create($data)){
            return redirect(route('admin.group'))->with(['success'=>'添加成功']);
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
        $model = Group::findOrFail($id);
        return view('admin.group.edit',compact('model'));
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
        $model = Group::findOrFail($id);
        $data = $request->all(['display_name','name']);
        if ($model->update($data)){
            return redirect(route('admin.group'))->with(['success'=>'更新成功']);
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
        if (Group::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }

    public function sip($id)
    {
        $group = Group::with('sips')->findOrFail($id);
        $sips = Sip::orderByDesc('id')->get();
        return view('admin.group.sip',compact('group','sips'));
    }

    public function assignSip(Request $request,$id)
    {
        $group = Group::findOrFail($id);
        $sips = $request->get('sips',[]);
        if ($group->sips()->sync($sips)){
            return redirect(route('admin.group'))->with(['success'=>'更新成功']);
        }
        return back()->withErrors(['error'=>'更新失败']);
    }


}
