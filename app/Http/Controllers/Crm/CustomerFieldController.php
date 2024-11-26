<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CustomerField;
use App\Models\CustomerFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CustomerFieldController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $res = CustomerField::query()
                ->orderBy('sort','asc')
                ->orderBy('id','desc')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.customer_field.index');
    }

    public function create()
    {
        return View::make('crm.customer_field.create');
    }

    public function store(Request $request)
    {
        $data = $request->all(['field_label','field_key','field_type','field_option','field_value','field_tips','sort','visiable']);
        //验证field_key是否重复
        $has_exist = CustomerField::where('field_key',$data['field_key'])->count();
        if ($has_exist){
            return $this->error('字段Key：'.$data['field_key'].'已存在');
        }
        try{
            CustomerField::create($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('添加客户字段异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function edit($id)
    {
        $model = CustomerField::findOrFail($id);
        return View::make('crm.customer_field.edit',compact('model'));
    }

    public function update(Request $request,$id)
    {
        $model = CustomerField::findOrFail($id);
        $data = $request->all(['field_label','field_key','field_type','field_option','field_value','field_tips','sort','visiable','required']);
        //验证field_key是否重复
        $has_exist = CustomerField::where('field_key',$data['field_key'])->where('id','!=',$id)->count();
        if ($has_exist){
            return $this->error('字段Key：'.$data['field_key'].'已存在');
        }
        try{
            $model->update($data);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('更新客户字段异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->get('ids',[]);
        //删除
        DB::beginTransaction();
        try{
            CustomerField::query()->whereIn('id',$ids)->delete();
            CustomerFieldValue::query()->whereIn('customer_field_id',$ids)->delete();
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除客户字段异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
