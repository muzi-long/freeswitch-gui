<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\CustomerField;
use App\Models\Customer;
use App\Models\CustomerFieldValue;
use App\Models\User;
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
    public function index(Request $request)
    {
        $users = User::query()->get();
        if ($request->ajax()){
            $data = $request->all(['name','contact_name','contact_phone']);
            $res = Customer::query()
                //客户名称
                ->when($data['name'], function ($query) use ($data) {
                    return $query->where('name', $data['name']);
                })
                //联系电话
                ->when($data['contact_phone'], function ($query) use ($data) {
                    return $query->where('contact_phone', $data['contact_phone']);
                })
                //联系人
                ->when($data['contact_name'], function ($query) use ($data) {
                    return $query->where('contact_name', $data['contact_name'] );
                })
                ->where('status','=',1)
                ->orderByDesc('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.assignment.index',compact('users'));
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
                'uuid' => uuid_generate(),
                'name' => $data['name'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'created_user_id' => $user->id,
                'created_user_nickname' => $user->nickname,
                'owner_user_id' => $user->id,
                'owner_user_nickname' => $user->nickname,
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
        $model = Customer::query()->findOrFail($id);
        $fields = CustomerField::query()
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        $data = CustomerFieldValue::query()->where('customer_id','=',$model->id)->pluck('data','customer_field_id')->toArray();
        return View::make('crm.assignment.edit',compact('model','fields','data'));
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
                DB::table('customer_field_value')
                    ->where('customer_id','=',$id)
                    ->where('customer_field_id',$d['customer_field_id'])
                    ->update(['data'=>$d['data']]);
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


    public function to(Request $request)
    {
        $ids = $request->get('ids',[]);
        $user = User::where('id',$request->get('user_id'))->first();
        $department_id = $request->get('department_id');
        $user_ids = User::where('department_id',$request->department_id)->pluck('id')->toArray();
        $type = $request->get('type');
        DB::beginTransaction();
        try{
            if ($type=='user'){
                DB::table('project')->whereIn('id',$ids)->update([
                    'owner_user_id' => -3,
                    'assignment_time' => date('Y-m-d H:i:s'),
                    'department_id' => $user->department_id,
                ]);
            }elseif ($type=='department'){
                DB::table('project')->whereIn('id',$ids)->update([
                    'owner_user_id' => -2,
                    'department_id' => $department_id,
                    'assignment_time' => date('Y-m-d H:i:s'),
                ]);
            }
            DB::commit();

            return Response::json(['code'=>0,'msg'=>'分配成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('分配异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'分配失败']);
        }
    }

}
