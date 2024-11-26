<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Sip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class AudioController extends Controller
{

    public function index(Request $request)
    {
        $data = DB::table('sip')
            ->join('user','sip.id','=','user.sip_id')
            ->select(['user.nickname','sip.id','sip.username','sip.status','sip.state'])
            ->get();
        $count = [
            'offline' => 0,
            'online' => 0,
            'down' => 0,
            'active' => 0,
        ];
        foreach ($data as $d){
            $d->status_name = '';
            $d->class_name = '';
            if ($d->status==0){
                $count['offline']++;
                $d->status_name = '离线';
                $d->class_name = 'status-offline';
            }
            if ($d->status==1){
                $count['online']++;
                $d->status_name = '在线';
                $d->class_name = 'status-online';
            }
            if ($d->status==1 && $d->state=='down'){
                $count['down']++;
                $d->status_name = '空闲';
                $d->class_name = 'status-down';
            }
            if ($d->status==1 && $d->state=='active'){
                $count['active']++;
                $d->status_name = '通话中';
                $d->class_name = 'status-active';
            }
        }
        if ($request->ajax()){
            return $this->success('ok',['count'=>$count,'data'=>$data]);
        }
        return View::make('chat.audio.index',compact('count','data'));
    }


}
