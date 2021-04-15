<?php

namespace App\Http\Controllers\Home;

use App\Models\Merchant;
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
        return View::make('home.remind.index');
    }

    public function data(Request $request)
    {
        // 1 今日待跟进， 2 超期待跟进
        $type = $request->get('type',1);
        $user = Auth::guard('merchant')->user();
        if ($user->merchant_id==0){
            $merchant_id = $user->id;
        }else{
            $merchant_id = $user->merchant_id;
        }
        $memberIds = Merchant::where('merchant_id',$merchant_id)->pluck('id');
        $model = (new Project())->where(function ($query) use($memberIds){
            return $query->whereIn('created_merchant_id',$memberIds);
        })->whereNotNull('next_follow_at');
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
        $user = Auth::guard('merchant')->user();
        if ($user->merchant_id==0){
            $merchant_id = $user->id;
        }else{
            $merchant_id = $user->merchant_id;
        }
        $memberIds = Merchant::where('merchant_id',$merchant_id)->pluck('id');
        $data = [];
        $nodes = Node::with(['projects'=>function($query) use($memberIds){
            $query->whereIn('created_merchant_id',$memberIds);
        }])->orderBy('sort','asc')->get();
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
