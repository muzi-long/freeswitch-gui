<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class MessageController extends Controller
{

    public function index(Request $request)
    {
        if ($request->ajax()){
            $type = $request->input('type');
            $res = Message::query();
            if ($type==2){ //通知
                $res = $res->where('send_user_id','=',0)
                    ->where('accept_user_id','=',$request->user()->id)
                    ->where('read','=',0);
            }elseif ($type==3){ //私信
                $res = $res->where('send_user_id','>',0)->where('accept_user_id','=',$request->user()->id);
            }
            $res = $res->orderBy('read','asc')->orderByDesc('id')->paginate($request->input('limit',30));
            return $this->success('ok',$res->items(),$res->total());
        }
        return View::make('chat.message.index');
    }


    public function create()
    {
        return View::make('chat.message.create');
    }


    public function store(Request $request)
    {
        $data = $request->all(['title','content','user_id']);
        try {
            $res = push_message('msg',
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                ],
                [$data['user_id']],
                $request->user()->id
            );
        } catch (GuzzleException $e) {
            $res = false;
        }
        if ($res){
            return $this->success();
        }
        return $this->error();
    }


    public function read(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids)){
            return $this->error('请选择操作项');
        }
        try {
            Message::query()->whereIn('id',$ids)->update(['read'=>1]);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('标记为已读操作异常：'.$exception->getMessage());
            return $this->error();
        }
    }


    public function show(Request $request,$id)
    {
        $model = Message::query()->where('id',$id)->first();
        return View::make('chat.message.show',compact('model'));
    }


    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        if (empty($ids)){
            return $this->error('请选择删除项');
        }
        try{
            Message::destroy($ids);
            return $this->success();
        }catch (\Exception $exception){
            Log::error('删除消息异常：'.$exception->getMessage());
            return $this->error();
        }
    }

}
