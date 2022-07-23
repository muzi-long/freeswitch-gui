<?php

namespace App\Http\Controllers\Call;

use App\Http\Controllers\Controller;
use App\Models\Cdr;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CdrController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()->get();
        if ($request->ajax()){
            $data = $request->all(['start_at','end_at','user_id','caller','callee']);
            $res = Cdr::query()
                ->when($data['start_at']&&!$data['end_at'],function ($q) use ($data){
                    return $q->where('start_time','>=',$data['start_at']);
                })
                ->when(!$data['start_at']&&$data['end_at'],function ($q) use ($data){
                    return $q->where('start_time','<=>',$data['end_at']);
                })
                ->when($data['start_at']&&$data['end_at'],function ($q) use ($data){
                    return $q->whereBetween('start_time',[$data['start_at'],$data['end_at']]);
                })
                ->when($data['user_id'],function ($q) use ($data){
                    return $q->where('user_id',$data['user_id']);
                })
                ->when($data['caller'],function ($q) use ($data){
                    return $q->where('caller',$data['caller']);
                })
                ->when($data['callee'],function ($q) use ($data){
                    return $q->where('callee',$data['callee']);
                })
                ->orderByDesc('id')
                ->paginate($request->get('limit', 30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('call.cdr.index',compact('users'));
    }
}
