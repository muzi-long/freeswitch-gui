<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Node;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class RemindController extends Controller
{

    /**
     * 跟进提醒列表
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return View::make('admin.remind.index');
    }

    public function data(Request $request)
    {
        // 1 今日待跟进， 2 超期待跟进
        $type = $request->get('type',1);
        $model = Project::query()
            ->where('owner_user_id',$request->user()->id)
            ->whereNotNull('next_follow_at');
        if ($type==1){
            $model = $model->whereBetween('next_follow_at',[Carbon::today(),Carbon::tomorrow()]);
        }elseif ($type==2){
            $model = $model->where('next_follow_at','<',Carbon::now());
        }
        $res = $model->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items()
        ];
        return Response::json($data);
    }

    /**
     * 各节点分布
     * @return \Illuminate\Http\JsonResponse
     */
    public function count()
    {

        $data = [];
        $nodes = Node::with('projects')->orderBy('sort','asc')->get();
        foreach ($nodes as $node){
            $data[$node->name] = $node->projects->count();
        }
        return Response::json([
            'code' => 0,
            'msg' => '请求成功',
            'data' => $data,
        ]);
    }

}