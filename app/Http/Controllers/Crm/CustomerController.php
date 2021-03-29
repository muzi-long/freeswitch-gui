<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerField;
use App\Models\CustomerFieldValue;
use App\Models\CustomerRemark;
use App\Models\Node;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class CustomerController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $data = $request->all([
                'name',
                'contact_name',
                'contact_phone',
                'node_id',
                'follow_time_start',
                'follow_time_end',
                'follow_user_id',
            ]);
            $user = $request->user();
            $res = Customer::query()
                ->where(function ($query) use($user){
                    if ($user->hasPermissionTo('crm.customer.list_all')) {
                        return $query->where('owner_user_id','>',0);
                    }elseif ($user->hasPermissionTo('crm.customer.list_department')) {
                        return $query->where('owner_department_id','=',$user->department_id);
                    }else{
                        return $query->where('owner_user_id',$user->id);
                    }
                })
                ->where('status','=',3)
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
                //节点
                ->when($data['node_id'],function ($query) use($data){
                    return $query->where('node_id',$data['node_id']);
                })
                //跟进时间
                ->when($data['follow_time_start']&&!$data['follow_time_end'],function ($query) use($data){
                    return $query->where('follow_time','>=',$data['follow_time_start']);
                })
                ->when(!$data['follow_time_start']&&$data['follow_time_end'],function ($query) use($data){
                    return $query->where('follow_time','<=',$data['follow_time_end']);
                })
                ->when($data['follow_time_start']&&$data['follow_time_end'],function ($query) use($data){
                    return $query->whereBetween('follow_time',[$data['follow_time_start'],$data['follow_time_end']]);
                })
                //跟进人
                ->when($data['follow_user_id'],function ($query) use($data){
                    return $query->where('follow_user_id',$data['follow_user_id']);
                })
                ->orderBy('is_end','asc')
                ->orderBy('status_time','desc')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('crm.customer.index');
    }


    public function create()
    {
        $fields = CustomerField::query()
            ->where('visiable',1)
            ->orderBy('sort','asc')
            ->get();
        return View::make('crm.customer.create',compact('fields'));
    }


    public function store(Request $request)
    {
        $user = $request->user();
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
                'owner_department_id' => $user->department_id??0,
                'assignment_user_id' => $user->id,
                'assignment_user_nickname' => $user->nickname,
                'status' => 3,
                'status_time' => date('Y-m-d H:i:s'),
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
            Log::error('客户录入个人库异常：'.$exception->getMessage());
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
        return View::make('crm.customer.edit',compact('model','fields','data'));
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
            Log::error('更新个人库客户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function destroy(Request $request)
    {
        $ids = $request->input('ids');
        try {
            Customer::query()->whereIn('id',$ids)->delete();
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除个人库客户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function transfer(Request $request)
    {
        $ids = $request->get('ids',[]);
        $user = User::where('id',$request->get('user_id'))->first();
        if ($user == null){
            return $this->error('请选择员工');
        }
        DB::beginTransaction();
        try{
            $data = [
                'owner_user_id' => $user->id,
                'owner_user_nickname' => $user->nickname,
                'owner_department_id' => $user->department_id??0,
            ];
            Customer::query()->whereIn('id',$ids)->update($data);
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('移交异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function remove(Request $request)
    {
        $customer_ids = $request->input('customer_ids');
        if (!is_array($customer_ids) || empty($customer_ids)){
            return $this->error('请选择剔除项');
        }
        try {
            Customer::query()->whereIn('id',$customer_ids)->update([
                'status' => 5,
                'status_time' => date('Y-m-d H:i:s'),
                'owner_user_id' => 0,
                'owner_user_nickname' => null,
                'owner_department_id' => 9,
            ]);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('剔除客户异常：'.$exception->getMessage());
            return $this->error();
        }
    }

    public function remark(Request $request,$id)
    {
        $customer = Customer::query()->where('id','=',$id)->first();
        $nodes = Node::query()->whereIn('type',[1,2])->orderBy('sort','asc')->get();

        if ($request->ajax()){
            $data = $request->all(['node_id','content','next_follow_time']);
            $old_node_id = $customer->node_id;
            $old_node_name = null;
            $new_node_id = $data['node_id']??0;
            $new_node_name = null;
            foreach ($nodes as $node){
                if ($node->id == $old_node_id){
                    $old_node_name = $node->name;
                }
                if ($node->id == $new_node_id){
                    $new_node_name = $node->name;
                }
            }
            DB::beginTransaction();
            try {
                $customer->update([
                    'follow_time' => date('Y-m-d H:i:s'),
                    'node_id' => $new_node_id,
                    'node_name' => $new_node_name,
                    'follow_user_id' => $request->user()->id,
                    'follow_user_nickname' => $request->user()->nickname,
                    'next_follow_time' => $data['next_follow_time'],
                    'remark' => $data['content'],
                ]);
                CustomerRemark::create([
                    'customer_id' => $customer->id,
                    'old_node_id' => $old_node_id,
                    'old_node_name' => $old_node_name,
                    'new_node_id' => $new_node_id,
                    'new_node_name' => $new_node_name,
                    'content' => $data['content'],
                    'next_follow_time' => $data['next_follow_time'],
                    'user_id' => $request->user()->id,
                    'user_nickname' => $request->user()->nickname,
                ]);
                DB::commit();
                return $this->success();
            }catch (\Exception $exception){
                DB::rollBack();
                Log::error('备注跟进客户异常：'.$exception->getMessage());
                return $this->error();
            }

        }
        return View::make('crm.customer.remark',compact('customer','nodes'));
    }


    public function show(Request $request,$id)
    {
        $model = Customer::with('fields')->where('id','=',$id)->first();
        return View::make('crm.customer.show',compact('model'));
    }


}
