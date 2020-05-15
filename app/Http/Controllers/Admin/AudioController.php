<?php

namespace App\Http\Controllers\Admin\pbx;

use App\Models\Audio;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $res = Audio::orderBy('id')->paginate($request->get('limit', 30));
            $data = [
                'code' => 0,
                'msg' => '正在请求中...',
                'count' => $res->total(),
                'data' => $res->items(),
            ];
            return response()->json($data);
        }
        return view('admin.audio.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $text = $request->get('text');
        if ($text==null){
            return response()->json(['code'=>1,'msg'=>'请输入待合成文本']);
        }
        try{
            $model = new Audio();
            $res = $model->tts($text);
            if ($res['code']==0) {
                Audio::create([
                    'url' => $res['data']['url'],
                    'path' => $res['data']['path'],
                    'text' => $text,
                    'user_id' => auth()->user()->id,
                ]));
                return response()->json(['code'=>0,'msg'=>'合成成功','data'=>['url'=>$res['data']['url']]]);
            }
            return response()->json($res);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'合成失败']);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $ids = $request->get('ids');
        $audio = Audio::where('id',$ids[0])->first();
        if ($ids == null){
            return response()->json(['code'=>1,'msg'=>'请选择删除项']);
        }
        try{
            if (file_exists($audio->path)){
                unlink($audio->path);
            }
            $audio->delete();
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'删除失败']);
        }
    }


}
