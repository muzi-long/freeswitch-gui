<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class NodeController extends Controller
{

    /**
     * 列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all(['merchant_id']);
            $res = Node::with('merchant')
                ->when($data['merchant_id'],function ($q) use($data){
                    return $q->where('merchant_id',$data['merchant_id']);
                })
                ->orderBy('merchant_id')
                ->orderBy('sort')
                ->orderBy('id')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.crm.node.index',compact('merchants'));
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.crm.node.create',compact('merchants'));
    }

    public function store(Request $request)
    {
        $data = $request->all(['merchant_id','name','sort']);
        $data['created_staff_id'] = 0;
        try{
            Node::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('backend.crm.node')]);
        }catch (\Exception $exception){
            Log::error('添加节点异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'添加失败']);
        }
    }

    /**
     * 更新
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Node::findOrFail($id);
        $merchants = Merchant::orderBy('id','desc')->get();
        return View::make('backend.crm.node.edit',compact('model','merchants'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','sort']);
        $model = Node::findOrFail($id);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('backend.crm.node')]);
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
