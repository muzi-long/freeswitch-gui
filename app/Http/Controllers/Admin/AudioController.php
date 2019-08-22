<?php

namespace App\Http\Controllers\Admin;

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
    public function index()
    {
        return view('admin.audio.index');
    }

    public function data(Request $request)
    {
        $res = Audio::orderBy('id')->paginate($request->get('limit', 30));
        $data = [
            'code' => 0,
            'msg' => '正在请求中...',
            'count' => $res->total(),
            'data' => $res->items(),
        ];
        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
        $url = 'http://api.xfyun.cn/v1/service/v1/tts';
        $appid = config('freeswitch.xfyun.appid');
        $apikey = config('freeswitch.xfyun.apikey');
        $param = array (
            'auf' => 'audio/L16;rate=8000',
            'aue' => 'raw',
            'voice_name' => 'xiaoyan',
            'speed' => '50',  //这三个参数必需是字符串
            'volume' => '50', //这三个参数必需是字符串
            'pitch' => '50',  //这三个参数必需是字符串
            'engine_type' => 'intp65',
        );
        $time = (string)time();
        $xparam = base64_encode(json_encode(($param)));
        $checksum = md5($apikey.$time.$xparam);
        $header = array(
            'X-CurTime:'.$time,
            'X-Param:'.$xparam,
            'X-Appid:'.$appid,
            'X-CheckSum:'.$checksum,
            'X-Real-Ip:127.0.0.1',
            'Content-Type:application/x-www-form-urlencoded; charset=utf-8'
        );
        $content = [
            'text' => $text,
        ];
        try{
            $response = $this->tocurl($url, $header, $content);
            $header = $response['header'];
            if($header['content_type'] == 'audio/mpeg'){
                $ext = $param['aue']=='raw'?'.wav':'.mp3';
                $filename = config('freeswitch.xfyun.sounds').$time.$ext;
                file_put_contents($filename, $response['body']);
                Audio::create(array_merge($param,[
                    'url' => $filename,
                    'text' => $text
                ]));
                return response()->json(['code'=>0,'msg'=>'合成成功','data'=>['url'=>$filename]]);
            }
            return response()->json(['code'=>1,'msg'=>'合成失败','data'=>json_decode($response['body'],true)]);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'合成失败：'.$exception->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
            if (file_exists($audio->url)){
                unlink($audio->url);
            }
            $audio->delete();
            return response()->json(['code'=>0,'msg'=>'删除成功']);
        }catch (\Exception $exception){
            return response()->json(['code'=>1,'msg'=>'删除失败：'.$exception->getMessage()]);
        }
    }

    public function tocurl($url, $header, $content){
        $ch = curl_init();
        if(substr($url,0,5)=='https'){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
        $response = curl_exec($ch);
        $error=curl_error($ch);
        //var_dump($error);
        if($error){
            die($error);
        }
        $header = curl_getinfo($ch);

        curl_close($ch);
        $data = array('header' => $header,'body' => $response);
        return $data;
    }

}
