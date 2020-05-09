<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\Department;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    //后台布局
    public function layout()
    {
        return View::make('admin.layout');
    }

    public function index()
    {
        //部门数量
        $departmentCount = Department::count();
        //用户数量
        $userCount = User::where('id','!=',config('freeswitch.user_root_id'))->count();
        //总客户数
        $projectCount = Project::count();
        //公海客户数
        $wasteCount = Project::onlyTrashed()->count();
        return View::make('admin.index.index',compact('departmentCount','userCount','projectCount','wasteCount'));
    }

    public function chart()
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
        Cdr::whereYear('aleg_start_at', date('Y'))
            ->orderBy('id')
            ->chunk(1000, function ($result) use(&$data){
                foreach ($result as $item) {
                    foreach ($data['year_month'] as $key=>$time){
                        if (strtotime($item->aleg_start_at)>=$time['start'] && strtotime($item->aleg_start_at)<$time['end']){
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
    
}
