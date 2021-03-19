<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Node;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class NodeController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = Node::query()
                ->orderBy('type','asc')
                ->orderBy('sort','asc')
                ->orderBy('id','asc')
                ->get();
            return $this->success('ok',$res,$res->count());
        }
        return View::make('crm.node.index');
    }

    public function create()
    {
        return View::make('crm.node.create');
    }

    public function store(Request $request)
    {
        $data = $request->all(['name','sort','type']);
        try{
            Node::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加节点异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = Node::findOrFail($id);
        return View::make('crm.node.edit',compact('model'));
    }

    public function update(Request $request,$id)
    {
        $data = $request->all(['name','sort','type']);
        $model = Node::findOrFail($id);
        try{
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新节点异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return $this->error('请选择删除项');
        }
        try{
            Node::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除节点异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
