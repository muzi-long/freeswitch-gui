<?php

namespace App\Http\Controllers;

use App\Models\Cdr;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Node;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IndexController extends Controller
{

    public function index(Request $request)
    {

        $user = $request->user();
        $data = [
            'sip_id' => $user->sip->id ?? 0,
            'username' => $user->sip->username ?? null,
            'password' => $user->sip->password ?? null,
            'host' => config('freeswitch.host'),
            'uri' => $user->sip->username . '@' . config('freeswitch.host'),
            'wss_url' => config('freeswitch.wss_url'),
        ];
        return View::make("layout", compact('data'));
    }

    public function console()
    {
        //部门数量
        $departmentCount = Department::count();
        //用户数量
        $userCount = User::count();
        //总客户数
        $customerCount = Customer::count();
        //公海客户数
        $wasteCount = Customer::where('status','=',5)->count();
        return View::make("index.console",compact('departmentCount','userCount','customerCount','wasteCount'));
    }

    public function cdrCount()
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
        Cdr::whereYear('created_at', date('Y'))
            ->orderBy('id')
            ->chunk(1000, function ($result) use(&$data){
                foreach ($result as $item) {
                    foreach ($data['year_month'] as $key=>$time){
                        if (strtotime($item->created_at)>=$time['start'] && strtotime($item->created_at)<$time['end']){
                            $data['calls'][$key] += 1;
                            if ($item->billsec>0){
                                $data['success'][$key] += 1;
                            }
                        }
                    }
                }
            });
        return $this->success('ok',$data);
    }

    public function customerCount()
    {
        $data = [];
        $nodes = Node::with('customer')->whereIn('type',[1,2])->orderBy('sort','asc')->get();
        foreach ($nodes as $node){
            $data[$node->name] = $node->customer->count();
        }
        return $this->success('ok',$data);
    }

    public function onlinecall()
    {
        return View::make('index.onlinecall');
    }

}
