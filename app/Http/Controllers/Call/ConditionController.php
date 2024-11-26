<?php

namespace App\Http\Controllers\Call;

use App\Models\Condition;
use App\Models\Extension;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class ConditionController extends Controller
{

    public function index(Request $request,$extension_id)
    {
        if ($request->ajax()){
            $res = Condition::query()
                ->where('extension_id',$extension_id)
                ->orderBy('sort')
                ->orderBy('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        $extension = Extension::findOrFail($extension_id);
        return View::make('call.dialplan.condition.index',compact('extension'));
    }


    public function create($extension_id)
    {
        $extension = Extension::findOrFail($extension_id);
        return View::make('call.dialplan.condition.create',compact('extension'));
    }


    public function store(Request $request,$extension_id)
    {
        $extension = Extension::findOrFail($extension_id);
        $data = $request->all(['display_name','field','expression','break','sort']);
        $data['extension_id'] = $extension->id;
        try {
            Condition::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加拨号条件异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function edit($extension_id,$id)
    {
        $extension = Extension::findOrFail($extension_id);
        $model = Condition::findOrFail($id);
        return View::make('call.dialplan.condition.edit',compact('extension','model'));
    }


    public function update(Request $request, $extension_id, $id)
    {
        $extension = Extension::findOrFail($extension_id);
        $model = Condition::findOrFail($id);
        $data = $request->all(['display_name','field','expression','break','sort']);
        try {
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新拨号条件异常：'.$exception->getMessage());
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
            Condition::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除拨号条件异常：'.$exception->getMessage());
            return $this->error();
        }
    }
}
