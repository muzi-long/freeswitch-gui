<?php

namespace App\Http\Controllers\Call;

use App\Models\Action;
use App\Models\Condition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ActionController extends Controller
{

    public function index(Request $request,$condition_id)
    {
        if ($request->ajax()){
            $res = Action::query()
                ->where('condition_id',$condition_id)
                ->orderBy('sort')
                ->orderBy('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        $condition = Condition::findOrFail($condition_id);
        return View::make('call.dialplan.action.index',compact('condition'));
    }



    public function create($condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        return View::make('call.dialplan.action.create',compact('condition'));
    }


    public function store(Request $request,$condition_id)
    {
        $condition = Condition::findOrFail($condition_id);
        $data = $request->all(['display_name','application','data','sort']);
        $data['condition_id'] = $condition->id;
        try {
            Action::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加拨号应用异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function edit($condition_id,$id)
    {
        $condition = Condition::findOrFail($condition_id);
        $model = Action::findOrFail($id);
        return View::make('call.dialplan.action.edit',compact('condition','model'));
    }


    public function update(Request $request, $condition_id, $id)
    {
        $condition = Condition::findOrFail($condition_id);
        $model = Action::findOrFail($id);
        $data = $request->all(['display_name','application','data','sort']);
        try {
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新拨号应用异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return $this->error('请选择删除项');
        }
        try {
            Action::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除拨号应用异常：'.$exception->getMessage());
            return $this->error();
        }
    }
}
