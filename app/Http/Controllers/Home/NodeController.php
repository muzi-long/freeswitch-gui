<?php

namespace App\Http\Controllers\Home;

use App\Http\Requests\NodeRequest;
use App\Models\Node;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

class NodeController extends Controller
{

    /**
     * 节点列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('home.node.index');
    }

    public function data(Request $request)
    {
        if (Auth::guard('merchant')->user()->merchant_id==0){
            $merchant_id = Auth::guard('merchant')->user()->id;
        }else{
            $merchant_id = Auth::guard('merchant')->user()->merchant_id;
        }
        $res = Node::where('merchant_id',$merchant_id)->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ];
        return Response::json($data);
    }

    /**
     * 添加节点
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return View::make('home.node.create');
    }

    /**
     * 添加节点
     * @param NodeRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(NodeRequest $request)
    {
        $data = $request->all(['name','sort']);
        $data['merchant_id'] = Auth::guard('merchant')->user()->id;
        try{
            Node::create($data);
            return Redirect::to(URL::route('home.node'))->with(['success'=>'添加成功']);
        }catch (\Exception $exception){
            Log::info('添加节点异常：'.$exception->getMessage());
            return Redirect::back()->withErrors('添加失败');
        }
    }

    /**
     * 更新节点
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $model = Node::findOrFail($id);
        return View::make('home.node.edit',compact('model'));
    }

    /**
     * 更新节点
     * @param NodeRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(NodeRequest $request,$id)
    {
        $model = Node::findOrFail($id);
        $data = $request->all(['name','sort']);
        try{
            $model->update($data);
            return Redirect::to(URL::route('home.node'))->with(['success'=>'更新成功']);
        }catch (\Exception $exception){
            Log::info('更新节点异常：'.$exception->getMessage());
            return Redirect::back()->withErrors('更新失败');
        }
    }

    /**
     * 删除节点
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $model = Node::findOrFail($ids[0]);
        //需要判断节点是否已被使用
        if (Project::where('node_id',$model->id)->count()){
            return Response::json(['code'=>1,'msg'=>'节点已被使用禁止删除']);
        }
        //删除
        try{
            $model->delete();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            Log::info('删除节点异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

}
