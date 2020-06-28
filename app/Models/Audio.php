<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Audio extends Model
{
    protected $table = 'audio';
    protected $fillable = ['text','url','path','user_id'];


    public function getAccessToken(){
    	$key = 'baidu_token';
    	$token = Redis::get($key);
    	if ($token == null) {
    		$client = new Client();
    		try{
    			$response = $client->get(config('freeswitch.baidu.url.token'),[
    				'query' => [
    					'grant_type' => 'client_credentials',
    					'client_id' => config('freeswitch.baidu.appKey'),
    					'client_secret' => config('freeswitch.baidu.appSecret'),
    				]
    			]);
    			$result = json_decode($response->getBody(),true);
    			$token = $result['access_token'];
    			Redis::setex($key,$result['expires_in']-100,$token);
    		}catch(\Exception $exception){
    			$token = null;
    		}
    	}
    	return $token;
    }

    public function tts($text){
    	$token = $this->getAccessToken();
    	if ($token==null) {
    		return ['code'=>1,'msg'=>'获取accessToken失败'];
    	}
    	$client = new Client();
		try{
			$response = $client->post(config('freeswitch.baidu.url.tts'),[
				'body' => http_build_query([
					'tex' => urlencode($text),
					'tok' => $token,
					'cuid' => 'freeswitch',
					'ctp' => 1,
					'lan' => 'zh',
					'spd' => 5, //语速0-15，默认为5中语速
					'pit' => 5, //音调0-15，默认为5中音调
					'vol' => 5, //音量0-15，默认为5中音量
					'aue' => 6, //3为mp3格式(默认)； 4为pcm-16k；5为pcm-8k；6为wav（内容同pcm-16k）; 注意aue=4或者6是语音识别要求的格式
					'per' => 0, //度小宇=1，度小美=0，度逍遥=3，度丫丫=4 ，度博文=106，度小童=110，度小萌=111，度米朵=103，度小娇=5
				])
			]);
			if (in_array('audio/wav',$response->getHeader('Content-Type'))){
				$file_url = Str::random().'.wav';
				$file_path = public_path('uploads').'/'.$file_url;
				Storage::disk('uploads')->put($file_url,$response->getBody());
				return ['code'=>0,'msg'=>'合成成功','data'=>['url'=>'/uploads/'.$file_url,'path'=>$file_path]];
			}
			return ['code'=>1,'msg'=>'合成失败','data'=>$response->getHeader('Content-Type')];

		}catch(\Exception $exception){
			return ['code'=>1,'msg'=>'合成异常','data'=>$exception->getMessage()];
		}

    }

    public function asr($file){
		$token = $this->getAccessToken();
    	if ($token==null) {
    		return ['code'=>1,'msg'=>'获取accessToken失败'];
    	}
    	if (!file_exists($file)) {
    		return ['code'=>1,'msg'=>'文件不存在'];
    	}
    	$client = new Client();
    	try{
			$response = $client->post(config('freeswitch.baidu.url.asr'),[
				'body' => base64_encode(file_get_contents($file)),
				'json' => http_build_query([
					'format' => 'wav',
					'rate' => 16000,
					'channel' => 1,
					'cuid' => 'freeswitch',
					'token' => $token,
					'dev_pid' => 1537 //语速0-15，默认为5中语速
				])
			]);
			$result = json_decode($response->getBody(),true);
			if (!$result['err_no']) {
				return ['code'=>0,'msg'=>'识别成功','data'=>$result['result'][0]];
			}
			return ['code'=>1,'msg'=>'识别失败'];

		}catch(\Exception $exception){
			return ['code'=>1,'msg'=>'识别异常'];
		}

    }

}
