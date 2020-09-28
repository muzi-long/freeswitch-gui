<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Project\ProjectRequest;
use App\Models\Node;
use App\Models\Order;
use App\Models\OrderFollow;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class OrderController extends Controller
{
    /**
     * 项目列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $nodes = Node::where('type',2)->orderBy('sort','asc')->get();
        $merchants = User::where('id','!=',config('freeswitch.user_root_id'))->get();
        return View::make('admin.order.index',compact('nodes','merchants'));
    }

    public function data(Request $request)
    {
        $user = Auth::user();
        $data = $request->all([
            'name',
            'phone',
            'follow_user_id',
            'created_user_id',
            'node_id',
            'follow_at_start',
            'follow_at_end',
            'next_follow_at_start',
            'next_follow_at_end',
            'created_at_start',
            'created_at_end',
        ]);
        $res = Order::with(['node','followUser','createUser','acceptUser'])

            ->where(function ($query) use($user){
                if ($user->hasPermissionTo('crm.order.list_all')) {
                    //return $query->where('backend_owner_user_id','>',0);
                }elseif ($user->hasPermissionTo('crm.order.list_department')) {
                    $user_ids = User::where('department_id',$user->department_id)->pluck('id')->toArray();
                    return $query->whereIn('accept_user_id',$user_ids);
                }else{
                    return $query->where('accept_user_id',$user->id);
                }
            })
            //姓名
            ->when($data['name'],function ($query) use($data){
                return $query->where('name',$data['name']);
            })
            //联系电话
            ->when($data['phone'],function ($query) use($data){
                return $query->where('phone',$data['phone']);
            })
            //节点
            ->when($data['node_id'],function ($query) use($data){
                return $query->where('node_id',$data['node_id']);
            })
            //跟进时间
            ->when($data['follow_at_start']&&!$data['follow_at_end'],function ($query) use($data){
                return $query->where('follow_at','>=',$data['follow_at_start']);
            })
            ->when(!$data['follow_at_start']&&$data['follow_at_end'],function ($query) use($data){
                return $query->where('follow_at','<=',$data['follow_at_end']);
            })
            ->when($data['follow_at_start']&&$data['follow_at_end'],function ($query) use($data){
                return $query->whereBetween('follow_at',[$data['follow_at_start'],$data['follow_at_end']]);
            })
            //下次跟进时间
            ->when($data['next_follow_at_start']&&!$data['next_follow_at_end'],function ($query) use($data){
                return $query->where('next_follow_at','>=',$data['next_follow_at_start']);
            })
            ->when(!$data['next_follow_at_start']&&$data['next_follow_at_end'],function ($query) use($data){
                return $query->where('next_follow_at','<=',$data['next_follow_at_end']);
            })
            ->when($data['next_follow_at_start']&&$data['next_follow_at_end'],function ($query) use($data){
                return $query->whereBetween('next_follow_at',[$data['next_follow_at_start'],$data['next_follow_at_end']]);
            })
            //创建时间
            ->when($data['created_at_start']&&!$data['created_at_end'],function ($query) use($data){
                return $query->where('created_at','>=',$data['created_at_start']);
            })
            ->when(!$data['created_at_start']&&$data['created_at_end'],function ($query) use($data){
                return $query->where('created_at','<=',$data['created_at_end']);
            })
            ->when($data['created_at_start']&&$data['created_at_end'],function ($query) use($data){
                return $query->whereBetween('created_at',[$data['created_at_start'],$data['created_at_end']]);
            })
            //成单人
            ->when($data['created_user_id'],function ($query) use($data){
                return $query->where('created_user_id',$data['created_user_id']);
            })
            //跟进人
            ->when($data['follow_user_id'],function ($query) use($data){
                return $query->where('follow_user_id',$data['follow_user_id']);
            })
            ->orderBy('accept_user_id','asc')
            ->orderBy('follow_at','desc')
            ->paginate($request->get('limit', 30));

        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ];
        return Response::json($data);
    }

    /**
     * 删除
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids',[]);
        $id = $ids[0];
        DB::beginTransaction();
        try{
            DB::table('order')->where('id',$id)->update([
                'deleted_at' => Carbon::now(),
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('删除项目异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'删除失败']);
        }
    }

    /**
     * 项目详情
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $order = Order::where('id',$id)->first();
        $model = Project::with('designs')->findOrFail($order->project_id);
        return View::make('admin.order.show',compact('model','order'));
    }

    /**
     * 分单
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function send(Request $request,$id)
    {
        $model = Order::where('id',$id)->first();
        $users = User::where('id','!=',config('freeswitch.user_root_id'))->get();
        if ($request->ajax()){
            $accept_user_id = $request->input('accept_user_id');
            try {
                Order::where('id',$id)->update([
                    'accept_user_id' => $accept_user_id,
                    'accept_time' => date('Y-m-d H:i:s'),
                    'accept_result' => 1,
                    'handle_user_id' => Auth::id(),
                    'handle_time' => date('Y-m-d H:i:s'),
                ]);
                return Response::json(['code'=>0,'msg'=>'分单成功']);
            }catch (\Exception $exception){
                return Response::json(['code'=>1,'msg'=>'分单失败']);
            }
        }
        return View::make('admin.order.send',compact('users','model'));
    }

    /**
     * 跟进
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function follow(Request $request, $id)
    {
        $order = Order::with('node')->where('id',$id)->first();

        $nodes = Node::where('type',2)->orderBy('sort','asc')->orderBy('id','asc')->pluck('name','id')->toArray();
        if ($request->ajax()){
            if (!$order->accept_user_id){
                return Response::json(['code'=>1,'msg'=>'未接单禁止跟进']);
        }
            $data = $request->all(['node_id','next_follow_at','remark']);
            DB::beginTransaction();
            try {
                DB::table('order_follow')->insert([
                    'order_id' => $id,
                    'old_node_id' => $order->node->id??0,
                    'old_node_name' => $order->node->name??0,
                    'new_node_id' => $data['node_id'],
                    'new_node_name' => $nodes[$data['node_id']],
                    'user_id' => Auth::id(),
                    'user_name' => Auth::user()->nickname,
                    'next_follow_time' => $data['next_follow_at'],
                    'remark' => $data['remark'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                DB::table('order')->where('id',$id)->update([
                    'node_id' => $data['node_id'],
                    'follow_at' => date('Y-m-d H:i:s'),
                    'follow_user_id' => Auth::id(),
                    'next_follow_at' => $data['next_follow_at'],
                    'remark' => $data['remark'],
                ]);
                DB::commit();
                return Response::json(['code'=>0,'msg'=>'跟进成功']);
            }catch (\Exception $exception){
                DB::rollBack();
                return Response::json(['code'=>1,'msg'=>'跟进失败']);
            }

        }
        return View::make('admin.order.follow',compact('order','nodes'));
    }

    /**
     * 跟进列表
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function followList(Request $request, $id)
    {
        $res = OrderFollow::query()
            ->orderBy('created_at','desc')
            ->paginate($request->get('limit', 30));

        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ];
        return Response::json($data);
    }


}
