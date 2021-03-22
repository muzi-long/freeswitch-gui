<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CustomerField;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class AssignmentController extends Controller
{
    /**
     * 待分配库列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function assignment(Request $request)
    {
        if ($request->ajax()){
            $res = Customer::query()
                ->where('status','=',1)
                ->orderByDesc()
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.assignment.index');
    }


    public function create()
    {
        $fields = CustomerField::query()
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('crm.assignment.create',compact('fields'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();
        $data = $request->all(['name','contact_name','contact_phone']);
        $dataInfo = [];
        $fields = CustomerField::query()->where('visiable','=',1)->get();
        foreach ($fields as $d){
            $items = [
                'customer_field_id' => $d->id,
                'data' => $request->get($d->field_key),
            ];
            if ($d->field_type=='checkbox'){
                if (!empty($items['data'])){
                    $items['data'] = implode(',',$items['data']);
                }else{
                    $items['data'] = null;
                }
            }
            array_push($dataInfo,$items);
        }
        DB::beginTransaction();
        try{
            $customer_id = DB::table('customer')->insertGetId([
                'name' => $data['name'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'created_user_id' => $user->id,
                'created_user_name' => $user->nickname,
                'owner_user_id' => $user->id,
                'created_user_name' => $user->nickname,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            foreach ($dataInfo as $d){
                DB::table('customer_field_value')->insert([
                    'customer_id' => $customer_id,
                    'customer_field_id' => $d['customer_field_id'],
                    'data' => $d['data'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('客户录入待分配库异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function edit($id)
    {
        $model = Customer::with('fields')->findOrFail($id);
        return View::make('crm.assignment.edit',compact('model'));
    }


    public function update(Request $request,$id)
    {
        $data = $request->all(['name','contact_name','contact_phone']);
        $dataInfo = [];
        $fields = CustomerField::query()->where('visiable','=',1)->get();
        foreach ($fields as $d){
            $items = [
                'customer_field_id' => $d->id,
                'data' => $request->get($d->field_key),
            ];
            if ($d->field_type=='checkbox'){
                if (!empty($items['data'])){
                    $items['data'] = implode(',',$items['data']);
                }else{
                    $items['data'] = null;
                }
            }
            array_push($dataInfo,$items);
        }

        DB::beginTransaction();
        try{
            DB::table('customer')->where('id',$id)->update([
                'name' => $data['name'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            foreach ($dataInfo as $d){
                DB::table('customer_field_value')->where('id',$d['id'])->update(['data'=>$d['data']]);
            }
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('更新待分配客户异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        try {
            Customer::query()->whereIn('id',$ids)->where('status','=',1)->delete();
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除待分配库客户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
