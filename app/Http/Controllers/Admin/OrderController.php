<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Project\ProjectRequest;
use App\Models\Node;
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
        $res = Project::with(['node','followUser'])
            ->where('is_end',1)
            ->where(function ($query) use($user){
                if ($user->hasPermissionTo('crm.order.list_all')) {
                    return $query->where('backend_owner_user_id','>',0);
                }elseif ($user->hasPermissionTo('crm.order.list_department')) {
                    $user_ids = User::where('department_id',$user->department_id)->pluck('id')->toArray();
                    return $query->whereIn('backend_owner_user_id',$user_ids);
                }else{
                    return $query->where('backend_owner_user_id',$user->id);
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
            ->orderBy('is_end','asc')
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
     * 删除项目
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids',[]);
        $id = $ids[0];
        DB::beginTransaction();
        try{
            DB::table('project')->where('id',$id)->update([
                'deleted_user_id' => Auth::guard()->user()->id,
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
        $model = Project::with('designs')->findOrFail($id);
        return View::make('admin.order.show',compact('model'));
    }

    /**
     * 更新节点
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function node($id)
    {
        $model = Project::findOrFail($id);
        $nodes = Node::where('type',2)->orderBy('sort','asc')->get();
        return View::make('admin.order.node',compact('model','nodes'));
    }

    /**
     * 更新节点
     * @param ProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function nodeStore(ProjectRequest $request,$id)
    {
        $model = Project::findOrFail($id);
        $data = $request->all(['node_id','content']);
        $old = $model->node_id;
        $user = Auth::user();
        DB::beginTransaction();
        try{
            DB::table('project_node')->insert([
                'project_id' => $id,
                'old' => $old,
                'new' => $data['node_id'],
                'content' => $data['content'],
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('project')->where('id',$id)->update([
                'node_id' => $data['node_id'],
                'updated_user_id' => $user->id,
                'updated_at' => Carbon::now()
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::info('更新节点异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }
    }

    /**
     * 更新备注
     * @param $id
     * @return \Illuminate\Contracts\View\View
     */
    public function remark($id)
    {
        $model = Project::findOrFail($id);
        return View::make('admin.order.remark',compact('model'));
    }

    /**
     * 更新备注
     * @param ProjectRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remarkStore(ProjectRequest $request,$id)
    {
        $model = Project::findOrFail($id);
        $data = $request->all(['next_follow_at','content']);
        $user = Auth::user();
        DB::beginTransaction();
        try{
            DB::table('project_remark')->insert([
                'project_id' => $id,
                'content' => $data['content'],
                'next_follow_at' => $data['next_follow_at'],
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            DB::table('project')->where('id',$id)->update([
                'next_follow_at' => $data['next_follow_at'],
                'follow_at' => Carbon::now(),
                'follow_user_id' => $user->id,
                'updated_user_id' => $user->id,
                'updated_at' => Carbon::now()
            ]);
            DB::commit();
            return Response::json(['code'=>0,'msg'=>'更新成功']);
        }catch (\Exception $exception){
            DB::rollBack();
            Log::error('更新备注异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'更新失败']);
        }

    }

}
