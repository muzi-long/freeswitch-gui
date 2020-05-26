<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class MonitorController extends Controller
{

    /**
     * 监听主页
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = Sip::query()->get();
        if ($request->ajax()){
            $list = [];
            $total = $ring = $active = $down = 0;
            foreach ($data as $d){
                $list[$d->id]['state'] = $d->state;
                $list[$d->id]['state_name'] = $d->state_name;
                $total++;
                if ($d->state == 'RINGING'){
                    $ring++;
                }elseif ($d->state == 'ACTIVE'){
                    $active++;
                }elseif ($d->state == 'DOWN' || $d->state == 'HANGUP'){
                    $down++;
                }
            }
            return Response::json([
                'code' => 0,
                'msg' => '请求成功',
                'data' => [
                    'count' => ['total' => $total, 'ring' => $ring, 'active' => $active, 'down' => $down],
                    'list' => $list,
                ]
            ]);
        }
        return View::make('admin.monitor.index',compact('data'));
    }

}
