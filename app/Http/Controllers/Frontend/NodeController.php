<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
            $res = Node::query()
                ->where('merchant_id',$request->user()->merchant_id)
                ->orderBy('sort')
                ->orderBy('id')
                ->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return Response::json($data);
        }
        return View::make('frontend.crm.node.index');
    }

    /**
     * 添加
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('frontend.crm.node.create');
    }

    public function store(Request $request)
    {
        $data = $request->all(['name','sort','type']);
        $data['created_staff_id'] = $request->user()->id;
        $data['merchant_id'] = $request->user()->merchant_id;
        try{
            Node::create($data);
            return Response::json(['code'=>0,'msg'=>'添加成功','url'=>route('frontend.crm.node')]);
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
        return View::make('frontend.crm.node.edit',compact('model'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','sort','type']);
        $model = Node::findOrFail($id);
        try{
            $model->update($data);
            return Response::json(['code'=>0,'msg'=>'更新成功','url'=>route('frontend.crm.node')]);
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
        try {
            Node::query()
                ->where('merchant_id',$request->user()->merchant_id)
                ->whereIn('id',$ids)
                ->delete();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::error('删除公海库记录异常：'.$exception->getMessage(),$ids);
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
