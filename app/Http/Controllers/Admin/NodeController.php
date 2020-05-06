<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class NodeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Node::orderBy('sort')->orderBy('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        $data = Node::orderBy('sort','asc')->select(['id','name','sort'])->get();
        return View::make('admin.node.index',compact('data'));
    }

    public function create()
    {
        return View::make('admin.node.create');
    }

    public function store(Request $request)
    {
        $data = $request->all(['name','sort']);
        try{
            Node::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功']);
        }catch (\Exception $exception){
            Log::error('添加节点异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    public function edit($id)
    {
        $model = Node::findOrFail($id);
        return View::make('admin.node.edit',compact('model'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','sort']);
        $model = Node::findOrFail($id);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            Log::error('更新节点异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return Response::json(['code'=>1,'msg'=>'请选择删除项']);
        }
        if (Node::destroy($ids)){
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }
        return Response::json(['code'=>1,'msg'=>'删除失败']);
    }


}
