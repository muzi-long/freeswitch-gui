<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\Department;
use App\Models\Merchant;
use App\Models\Node;
use App\Models\Project;
use App\Models\Sip;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{
    //后台布局
    public function layout()
    {
        return View::make('frontend.layout');
    }

    /**
     * 后台主页
     */
    public function index(Request $request)
    {
        $merchant = Merchant::find($request->user()->merchant_id);
        $staff_num = Staff::where('merchant_id',$merchant->id)->count();
        $sip_num = Sip::where('merchant_id',$merchant->id)->count();
        $department_num = Department::where('merchant_id',$merchant->id)->count();
        $project_num = Project::where('merchant_id',$merchant->id)->count();
        $sip = Sip::with('freeswitch')->where('staff_id',$request->user()->id)->first();
        return View::make('frontend.index.index',compact('merchant','staff_num','sip_num','department_num','project_num','sip'));
    }

    /**
     * 通话记录图表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cdr(Request $request)
    {
        $data = [
            'months' => [],
            'calls' => [],
            'success' => [],
        ];
        for ($m=1;$m<=12;$m++){
            $data['year_month'][$m] = [
                'start' => mktime(0,0,0,$m,1,date('Y')),
                'end' => mktime(0,0,0,$m+1,1,date('Y')),
            ];
            $data['months'][] = $m.'月';
            $data['calls'][$m] = 0;
            $data['success'][$m] = 0;
        }
        Cdr::whereYear('call_time', date('Y'))
            ->where('merchant_id',$request->user()->merchant_id)
            ->orderBy('id')
            ->chunk(1000, function ($result) use(&$data){
                foreach ($result as $item) {
                    foreach ($data['year_month'] as $key=>$time){
                        if (strtotime($item->call_time)>=$time['start'] && strtotime($item->call_time)<$time['end']){
                            $data['calls'][$key] += 1;
                            if ($item->billsec>0){
                                $data['success'][$key] += 1;
                            }
                        }
                    }
                }
            });
        return Response::json(['code'=>0,'msg'=>'请求成功','data'=>$data]);
    }

    /**
     * 节点客户图表
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function node(Request $request)
    {
        $data = [];
        $nodes = Node::with('projects')
            ->where('merchant_id',$request->user()->merchant_id)
            ->where('type',1)
            ->orderBy('sort','asc')
            ->get();
        foreach ($nodes as $node){
            $data[$node->name] = $node->projects->count();
        }
        return Response::json([
            'code' => 0,
            'msg' => '请求成功',
            'data' => $data,
        ]);
    }

    public function online()
    {
        return View::make('frontend.index.online');
    }


}
