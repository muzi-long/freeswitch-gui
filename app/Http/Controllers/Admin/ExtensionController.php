<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ExtensionRequest;
use App\Models\Extension;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExtensionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.dialplan.extension.index');
    }

    public function data(Request $request)
    {
        $query = Extension::query();
        $res = $query->orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
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
        return view('admin.dialplan.extension.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExtensionRequest $request)
    {
        $data = $request->all(['display_name','name','sort','continue','context']);
        if (Extension::create($data)){
            return redirect(route('admin.extension'))->with(['success'=>'添加成功']);
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
        $extension = Extension::with('conditions')->findOrFail($id);

        return view('admin.dialplan.extension.show',compact('extension'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $model = Extension::findOrFail($id);
        return view('admin.dialplan.extension.edit',compact('model'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ExtensionRequest $request, $id)
    {
        $model = Extension::findOrFail($id);
        $data = $request->all(['display_name','name','sort','continue','context']);
        if ($model->update($data)){
            return redirect(route('admin.extension'))->with(['success'=>'更新成功']);
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
        if (Extension::destroy($ids)){
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }
        return response()->json(['code'=>1,'msg'=>'删除失败']);
    }
}
