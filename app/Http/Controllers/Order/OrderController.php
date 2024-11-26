<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Node;
use App\Models\Order;
use App\Models\OrderPay;
use App\Models\OrderRemark;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $user = $request->user();
            $data = $request->all([
                'name',
                'contact_name',
                'contact_phone',
                'num',
            ]);
            $res = Order::query()
                ->where(function ($q) use($user){
                    if ($user->hasPermissionTo('order.order.list_all')) {

                    }elseif ($user->hasPermissionTo('order.order.list_department')) {
                        return $q->where('frontend_department_id',$user->department_id)->orWhere('backend_department_id',$user->department_id);
                    }else{
                        return $q->where('frontend_user_id',$user->department_id)->orWhere('backend_user_id',$user->department_id);
                    }
                })
                //订单号
                ->when($data['num'], function ($query) use ($data) {
                    return $query->where('num', $data['num']);
                })
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
                ->orderBy('status','asc')
                ->orderByDesc('accept_time')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('order.order.index');
    }

    public function create(Request $request)
    {
        $id = $request->input('customer_id');
        $model = Customer::query()->where('id',$id)->first();
        return View::make('order.order.create',compact('model'));
    }

    public function store(Request $request)
    {
        $id = $request->input('customer_id');
        $customer = Customer::query()->where('id',$id)->first();
        DB::beginTransaction();
        try {
            $data = $request->all([
                'total_money',
                'first_money',
                'mid_money',
                'last_money',
                'user_id',
            ]);
            if ($data['total_money'] != ($data['first_money']+$data['mid_money']+$data['last_money'])){
                return $this->error('订单金额比例不正确');
            }
            $user = User::query()->where('id',$data['user_id'])->first();
            if ($user == null){
                return $this->error('请选择接单人');
            }
            if ($customer->is_end!=1){
                $customer->update(['is_end'=>1]);
            }
            $order_num = create_order_num();
            Order::create([
                'num' => $order_num,
                'customer_id' => $customer->id,
                'name' => $customer->name,
                'contact_name' => $customer->contact_name,
                'contact_phone' => $customer->contact_phone,
                'total_money' => $data['total_money'],
                'first_money' => $data['first_money'],
                'mid_money' => $data['mid_money'],
                'last_money' => $data['last_money'],
                'payed_money' => 0,
                'frontend_department_id' => $customer->owner_department_id??0,
                'frontend_user_id' => $customer->owner_user_id,
                'frontend_user_nickname' => $customer->owner_user_nickname,
                'accept_time' => date('Y-m-d H:i:s'),
                'backend_department_id' => $user->department_id??0,
                'backend_user_id' => $user->id,
                'backend_user_nickname' => $user->nickname,
                'created_user_id' => $request->user()->id,
            ]);
            push_message(
                'msg',
                ['title'=>'分配订单提醒','content'=>'用户 '.$request->user()->nickname.' 给你分配了一个新的订单，订单号： '.$order_num],
                [$user->id]
            );
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('生成订单异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function remark(Request $request)
    {
        $id = $request->input('id');
        $model = Order::query()->where('id',$id)->first();
        $nodes = Node::query()->whereIn('type',[1,3])->orderBy('sort','asc')->get();
        if ($request->ajax()){
            $data = $request->all(['node_id','content','next_follow_time']);
            $old_node_id = $model->node_id;
            $old_node_name = '';
            $new_node_id = $data['node_id']??0;
            $new_node_name = '';
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
                $model->update([
                    'follow_time' => date('Y-m-d H:i:s'),
                    'node_id' => $new_node_id,
                    'node_name' => $new_node_name,
                    'follow_user_id' => $request->user()->id,
                    'follow_user_nickname' => $request->user()->nickname,
                    'next_follow_time' => $data['next_follow_time'],
                    'remark' => $data['content'],
                ]);
                OrderRemark::create([
                    'order_id' => $model->id,
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
                Log::error('备注跟进订单异常：'.$exception->getMessage());
                return $this->error();
            }
        }
        return View::make('order.order.remark',compact('model'));
    }


    public function payForm(Request $request)
    {
        $model = Order::query()->where('id',$request->input('id'))->first();
        return View::make('order.order.pay',compact('model'));
    }


    public function pay(Request $request)
    {
        $model = Order::query()->where('id',$request->input('id'))->first();
        DB::beginTransaction();
        try {
            $data = $request->all([
                'money',
                'pay_type',
                'content',
            ]);
            OrderPay::create([
                'order_id' => $model->id,
                'money' => $data['money'],
                'pay_type' => $data['pay_type'],
                'content' => $data['content'],
                'status' => 0,
                'created_user_id' => $request->user()->id,
                'created_user_nickname' => $request->user()->nickname,
            ]);
            DB::commit();
            return $this->success('操作成功，等待财务审核');
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('付款异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
