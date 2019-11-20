<?php

namespace App\Http\Controllers;

use App\Models\Cdr;
use App\Models\Extension;
use App\Models\Gateway;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\Sip;


class ApiController extends Controller
{

    //文件上传
    public function upload(Request $request)
    {
        //上传文件最大大小,单位M
        $maxSize = 10;
        //支持的上传图片类型
        $allowed_extensions = ["png", "jpg", "gif"];
        //返回信息json
        $data = ['code'=>1, 'msg'=>'上传失败', 'data'=>''];
        $file = $request->file('file');

        //检查文件是否上传完成
        if ($file->isValid()){
            //检测图片类型
            $ext = $file->getClientOriginalExtension();
            if (!in_array(strtolower($ext),$allowed_extensions)){
                $data['msg'] = "请上传".implode(",",$allowed_extensions)."格式的图片";
                return response()->json($data);
            }
            //检测图片大小
            if ($file->getSize() > $maxSize*1024*1024){
                $data['msg'] = "图片大小限制".$maxSize."M";
                return response()->json($data);
            }
        }else{
            $data['msg'] = $file->getErrorMessage();
            return response()->json($data);
        }
        $newFile = date('Y-m-d')."_".time()."_".uniqid().".".$file->getClientOriginalExtension();
        $disk = Storage::disk('uploads');
        $res = $disk->put($newFile,file_get_contents($file->getRealPath()));
        if($res){
            $data = [
                'code'  => 0,
                'msg'   => '上传成功',
                'data'  => $newFile,
                'url'   => '/uploads/local/'.$newFile,
            ];
        }else{
            $data['data'] = $file->getErrorMessage();
        }
        return response()->json($data);
    }

    /**
     * 商户网关关系
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function merchantGateway(Request $request)
    {
        $res = Merchant::with('gateways')
            ->where('merchant_id',0)
            ->select(['id','username as name'])
            ->get();
        return response()->json($res);

    }

    /**
     * 分机动态注册
     * @param Request $request
     * @return bool
     */
    public function directory(Request $request)
    {
        $user = $request->get('user');
        $sips = Sip::where('username',$user)->get();
        //$groups = Group::with('sips')->whereHas('sips')->get();

        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
        $xml .= "<document type=\"freeswitch/xml\">\n";
        $xml .= "<section name=\"directory\" >\n";
        $xml .= "<domain name=\"\$\${domain}\">\n";
        $xml .= "<params>\n";
        $xml .= "<param name=\"dial-string\" value=\"{presence_id=\${dialed_user}@\${dialed_domain}}\${sofia_contact(\${dialed_user}@\${dialed_domain})}\"/>\n";
        $xml .= "</params>\n";
        $xml .= "<groups>\n";

        //默认用户组default
        $xml .= "<group name=\"default\">\n";
        $xml .= "    <users>\n";
        foreach ($sips as $sip){
            $outbound_caller_id_number = $sip->outbound_caller_id_number??"\$\${outbound_caller_id}";
            $xml .= "    <user id=\"".$sip->username."\">\n";
            $xml .= "        <params>";
            $xml .= "           <param name=\"password\" value=\"".$sip->password."\"/>\n";
            $xml .= "           <param name=\"vm-password\" value=\"".$sip->password."\"/>\n";
            $xml .= "        </params>\n";
            $xml .= "        <variables>\n";
            $xml .= "        <variable name=\"toll_allow\" value=\"domestic,international,local\"/>\n";
            $xml .= "           <variable name=\"accountcode\" value=\"".$sip->username."\"/>\n";
            $xml .= "           <variable name=\"user_context\" value=\"".$sip->context."\"/>\n";
            $xml .= "           <variable name=\"effective_caller_id_name\" value=\"".$sip->effective_caller_id_name."\"/>\n";
            $xml .= "           <variable name=\"effective_caller_id_number\" value=\"".$sip->effective_caller_id_number."\"/>\n";
            $xml .= "           <variable name=\"outbound_caller_id_name\" value=\"\$\${outbound_caller_name}\"/>\n";
            $xml .= "           <variable name=\"outbound_caller_id_number\" value=\"".$outbound_caller_id_number."\"/>\n";
            $xml .= "        </variables>\n";
            $xml .= "    </user>";
        }
        $xml .= "    </users>\n";
        $xml .= "</group>\n";

        //自定义用户组
        /*foreach ($groups as $group){
            $xml .= "<group name=\"".$group->name."\">\n";
            $xml .= "    <users>\n";
            foreach ($group->sips as $sip){
                $xml .= "   <user id=\"".$sip->username."\" type=\"pointer\"/>";
            }
            $xml .= "    </users>\n";
            $xml .= "</group>\n";
        }*/

        $xml .= "</groups>\n";
        $xml .= "</domain>\n";
        $xml .= "</section>\n";
        $xml .= "</document>\n";
        return response($xml,200)->header("Content-type","text/xml");
    }

    /**
     * 动态拨号计划
     * @param Request $request
     * @return mixed
     */
    public function dialplan(Request $request)
    {
        if ($request->get('section')=='dialplan'){
            $context = $request->get('Caller-Context','default');

            $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
            $xml .= "<document type=\"freeswitch/xml\">\n";
            $xml .= "<section name=\"dialplan\" description=\"RE Dial Plan For FreeSwitch\">\n";
            $xml .= "<context name=\"".$context."\">\n";

            //拨号计划
            $extension = Extension::with('conditions')->whereHas('conditions')->where('context',$context)->orderBy('sort')->orderBy('id')->get();
            foreach ($extension as $exten){
                $xml .= "<extension name=\"" . $exten->name . "\" continue=\"" . $exten->continue . "\" >\n";
                if ($exten->conditions->isNotEmpty()){
                    foreach ($exten->conditions as $condition){
                        $xml .= "<condition field=\"" . $condition->field . "\" expression=\"" . $condition->expression . "\" break=\"" . $condition->break . "\">\n";
                        if ($condition->actions->isNotEmpty()){
                            foreach ($condition->actions as $action){
                                $xml .= "<action application=\"" . $action->application . "\" data=\"" . $action->data . "\" />\n";
                            }
                        }
                        $xml .= "</condition>\n";
                    }
                }
                $xml .= "</extension>\n";
            }

            $xml .= "</context>\n";
            $xml .= "</section>\n";
            $xml .= "</document>\n";
            return response($xml,200)->header("Content-type","text/xml");
        }
    }

    /**
     * 动态configuration 包含动态网关
     * @param Request $request
     * @return mixed
     */
    public function configuration(Request $request)
    {
        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
        $xml .= "<document type=\"freeswitch/xml\">\n";
        $xml .= "<section name=\"configuration\" description=\"FreeSwitch configuration\">\n";

        $xml .= "<configuration name=\"sofia.conf\" description=\"sofia Endpoint\">\n";
        $xml .= "    <global_settings>\n";
        $xml .= "       <param name=\"log-level\" value=\"0\"/>\n";
        $xml .= "       <!-- <param name=\"auto-restart\" value=\"false\"/> -->\n";
        $xml .= "       <param name=\"debug-presence\" value=\"0\"/>\n";
        $xml .= "       <!-- <param name=\"capture-server\" value=\"udp:homer.domain.com:5060\"/> -->\n";
        $xml .= "       <!-- <param name=\"capture-server\" value=\"udp:homer.domain.com:5060;hep=3;capture_id=100\"/> -->\n";
        $xml .= "    </global_settings>\n";
        $xml .= "    <profiles>\n";
        $xml .= "    <profile name=\"external\">\n";
        $xml .= "       <gateways>\n";
        $gateways = Gateway::orderByDesc('id')->get();
        foreach ($gateways as $gateway){
            $xml .= "           <gateway name=\"gw".$gateway->id."\">\n";
            $xml .= "               <param name=\"username\" value=\"".$gateway->username."\"/>\n";
            $xml .= "               <param name=\"realm\" value=\"".$gateway->realm."\"/>\n";
            $xml .= "               <param name=\"password\" value=\"".$gateway->password."\"/>\n";
            $xml .= "           </gateway>\n";
        }
        $xml .= "       </gateways>\n";
        $xml .= "       <aliases>\n";
        $xml .= "       </aliases>\n";
        $xml .= "       <domains>\n";
        $xml .= "           <domain name=\"all\" alias=\"false\" parse=\"true\"/>\n";
        $xml .= "       </domains>\n";
        $xml .= "       <settings>\n";
        $xml .= "           <param name=\"debug\" value=\"0\"/>\n";
        $xml .= "           <!-- If you want FreeSWITCH to shutdown if this profile fails to load, uncomment the next line. -->\n";
        $xml .= "           <!-- <param name=\"shutdown-on-fail\" value=\"true\"/> -->\n";
        $xml .= "           <param name=\"sip-trace\" value=\"no\"/>\n";
        $xml .= "           <param name=\"sip-capture\" value=\"no\"/>\n";
        $xml .= "           <param name=\"rfc2833-pt\" value=\"101\"/>\n";
        $xml .= "           <!-- RFC 5626 : Send reg-id and sip.instance -->\n";
        $xml .= "           <!--<param name=\"enable-rfc-5626\" value=\"true\"/> -->\n";
        $xml .= "           <param name=\"sip-port\" value=\"\$\${external_sip_port}\"/>\n";
        $xml .= "           <param name=\"dialplan\" value=\"XML\"/>\n";
        $xml .= "           <param name=\"context\" value=\"public\"/>\n";
        $xml .= "           <param name=\"dtmf-duration\" value=\"2000\"/>\n";
        $xml .= "           <param name=\"inbound-codec-prefs\" value=\"\$$\{global_codec_prefs}\"/>\n";
        $xml .= "           <param name=\"outbound-codec-prefs\" value=\"\$\${outbound_codec_prefs}\"/>\n";
        $xml .= "           <param name=\"hold-music\" value=\"\$\${hold_music}\"/>\n";
        $xml .= "           <param name=\"rtp-timer-name\" value=\"soft\"/>\n";
        $xml .= "           <!--<param name=\"enable-100rel\" value=\"true\"/>-->\n";
        $xml .= "           <!--<param name=\"disable-srv503\" value=\"true\"/>-->\n";
        $xml .= "           <!-- This could be set to \"passive\" -->\n";
        $xml .= "           <param name=\"local-network-acl\" value=\"localnet.auto\"/>\n";
        $xml .= "           <param name=\"manage-presence\" value=\"false\"/>\n";
        $xml .= "           <!-- Name of the db to use for this profile -->\n";
        $xml .= "           <!--<param name=\"dbname\" value=\"share_presence\"/>-->\n";
        $xml .= "           <!--<param name=\"presence-hosts\" value=\"\$\${domain}\"/>-->\n";
        $xml .= "           <!--<param name=\"force-register-domain\" value=\"\$\${domain}\"/>-->\n";
        $xml .= "           <!--all inbound reg will stored in the db using this domain -->\n";
        $xml .= "           <!--<param name=\"force-register-db-domain\" value=\"\$\${domain}\"/>-->  \n";
        $xml .= "           <!--<param name=\"aggressive-nat-detection\" value=\"true\"/>-->\n";
        $xml .= "           <param name=\"inbound-codec-negotiation\" value=\"generous\"/>\n";
        $xml .= "           <param name=\"nonce-ttl\" value=\"60\"/>\n";
        $xml .= "           <param name=\"auth-calls\" value=\"false\"/>\n";
        $xml .= "           <param name=\"inbound-late-negotiation\" value=\"true\"/>\n";
        $xml .= "           <param name=\"inbound-zrtp-passthru\" value=\"true\"/>\n";
        $xml .= "           <param name=\"rtp-ip\" value=\"\$\${local_ip_v4}\"/>\n";
        $xml .= "           <param name=\"sip-ip\" value=\"\$\${local_ip_v4}\"/>\n";
        $xml .= "           <param name=\"ext-rtp-ip\" value=\"auto-nat\"/>\n";
        $xml .= "           <param name=\"ext-sip-ip\" value=\"auto-nat\"/>\n";
        $xml .= "           <param name=\"rtp-timeout-sec\" value=\"300\"/>\n";
        $xml .= "           <param name=\"rtp-hold-timeout-sec\" value=\"1800\"/>\n";
        $xml .= "           <!--<param name=\"enable-3pcc\" value=\"true\"/>-->\n";
        $xml .= "           <param name=\"tls\" value=\"\$\${external_ssl_enable}\"/>\n";
        $xml .= "           <param name=\"tls-only\" value=\"false\"/>\n";
        $xml .= "           <param name=\"tls-bind-params\" value=\"transport=tls\"/>\n";
        $xml .= "           <param name=\"tls-sip-port\" value=\"\$\${external_tls_port}\"/>\n";
        $xml .= "           <!--<param name=\"tls-cert-dir\" value=\"\"/>-->\n";
        $xml .= "           <param name=\"tls-passphrase\" value=\"\"/>\n";
        $xml .= "           <!-- Verify the date on TLS certificates -->\n";
        $xml .= "           <param name=\"tls-verify-date\" value=\"true\"/>\n";
        $xml .= "           <param name=\"tls-verify-policy\" value=\"none\"/>\n";
        $xml .= "           <param name=\"tls-verify-depth\" value=\"2\"/>\n";
        $xml .= "           <param name=\"tls-verify-in-subjects\" value=\"\"/>\n";
        $xml .= "           <param name=\"tls-version\" value=\"\$\${sip_tls_version}\"/>\n";
        $xml .= "       </settings>\n";
        $xml .= "   </profile>\n";
        //$xml .=     file_get_contents('etc_freeswitch/sip_profiles/internal.xml');
        $xml .= "   </profiles>\n";
        $xml .= "</configuration>\n";
        $xml .= "</section>\n";
        $xml .= "</document>\n";
        return response($xml,200)->header("Content-type","text/xml");
    }

    /**
     * 动态configuration 包含动态网关
     * @param Request $request
     * @return mixed
     */
    public function configuration1(Request $request)
    {
        $xml  = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>\n";
        $xml .= "<document type=\"freeswitch/xml\">\n";
        $xml .= "<section name=\"configuration\" description=\"FreeSwitch configuration\">\n";

        foreach (scandir('etc_freeswitch/autoload_configs/') as $conf){
            if ($conf=='.'||$conf=='..')
            {
                continue;
            }elseif ($conf=='sofia.conf.xml') {

                $xml .= "<configuration name=\"sofia.conf\" description=\"sofia Endpoint\">\n";
                $xml .= "    <global_settings>\n";
                $xml .= "       <param name=\"log-level\" value=\"0\"/>\n";
                $xml .= "       <!-- <param name=\"auto-restart\" value=\"false\"/> -->\n";
                $xml .= "       <param name=\"debug-presence\" value=\"0\"/>\n";
                $xml .= "       <!-- <param name=\"capture-server\" value=\"udp:homer.domain.com:5060\"/> -->\n";
                $xml .= "       <!-- <param name=\"capture-server\" value=\"udp:homer.domain.com:5060;hep=3;capture_id=100\"/> -->\n";
                $xml .= "    </global_settings>\n";
                $xml .= "    <profiles>\n";
                $xml .= "    <profile name=\"external\">\n";
                $xml .= "       <gateways>\n";
                $gateways = Gateway::orderByDesc('id')->get();
                foreach ($gateways as $gateway){
                    $xml .= "           <gateway name=\"gw".$gateway->id."\">\n";
                    $xml .= "               <param name=\"username\" value=\"".$gateway->username."\"/>\n";
                    $xml .= "               <param name=\"realm\" value=\"".$gateway->realm."\"/>\n";
                    $xml .= "               <param name=\"password\" value=\"".$gateway->password."\"/>\n";
                    $xml .= "           </gateway>\n";
                }
                $xml .= "       </gateways>\n";
                $xml .= "       <aliases>\n";
                $xml .= "       </aliases>\n";
                $xml .= "       <domains>\n";
                $xml .= "           <domain name=\"all\" alias=\"false\" parse=\"true\"/>\n";
                $xml .= "       </domains>\n";
                $xml .= "       <settings>\n";
                $xml .= "           <param name=\"debug\" value=\"0\"/>\n";
                $xml .= "           <!-- If you want FreeSWITCH to shutdown if this profile fails to load, uncomment the next line. -->\n";
                $xml .= "           <!-- <param name=\"shutdown-on-fail\" value=\"true\"/> -->\n";
                $xml .= "           <param name=\"sip-trace\" value=\"no\"/>\n";
                $xml .= "           <param name=\"sip-capture\" value=\"no\"/>\n";
                $xml .= "           <param name=\"rfc2833-pt\" value=\"101\"/>\n";
                $xml .= "           <!-- RFC 5626 : Send reg-id and sip.instance -->\n";
                $xml .= "           <!--<param name=\"enable-rfc-5626\" value=\"true\"/> -->\n";
                $xml .= "           <param name=\"sip-port\" value=\"\$\${external_sip_port}\"/>\n";
                $xml .= "           <param name=\"dialplan\" value=\"XML\"/>\n";
                $xml .= "           <param name=\"context\" value=\"public\"/>\n";
                $xml .= "           <param name=\"dtmf-duration\" value=\"2000\"/>\n";
                $xml .= "           <param name=\"inbound-codec-prefs\" value=\"\$$\{global_codec_prefs}\"/>\n";
                $xml .= "           <param name=\"outbound-codec-prefs\" value=\"\$\${outbound_codec_prefs}\"/>\n";
                $xml .= "           <param name=\"hold-music\" value=\"\$\${hold_music}\"/>\n";
                $xml .= "           <param name=\"rtp-timer-name\" value=\"soft\"/>\n";
                $xml .= "           <!--<param name=\"enable-100rel\" value=\"true\"/>-->\n";
                $xml .= "           <!--<param name=\"disable-srv503\" value=\"true\"/>-->\n";
                $xml .= "           <!-- This could be set to \"passive\" -->\n";
                $xml .= "           <param name=\"local-network-acl\" value=\"localnet.auto\"/>\n";
                $xml .= "           <param name=\"manage-presence\" value=\"false\"/>\n";
                $xml .= "           <!-- Name of the db to use for this profile -->\n";
                $xml .= "           <!--<param name=\"dbname\" value=\"share_presence\"/>-->\n";
                $xml .= "           <!--<param name=\"presence-hosts\" value=\"\$\${domain}\"/>-->\n";
                $xml .= "           <!--<param name=\"force-register-domain\" value=\"\$\${domain}\"/>-->\n";
                $xml .= "           <!--all inbound reg will stored in the db using this domain -->\n";
                $xml .= "           <!--<param name=\"force-register-db-domain\" value=\"\$\${domain}\"/>-->  \n";
                $xml .= "           <!--<param name=\"aggressive-nat-detection\" value=\"true\"/>-->\n";
                $xml .= "           <param name=\"inbound-codec-negotiation\" value=\"generous\"/>\n";
                $xml .= "           <param name=\"nonce-ttl\" value=\"60\"/>\n";
                $xml .= "           <param name=\"auth-calls\" value=\"false\"/>\n";
                $xml .= "           <param name=\"inbound-late-negotiation\" value=\"true\"/>\n";
                $xml .= "           <param name=\"inbound-zrtp-passthru\" value=\"true\"/>\n";
                $xml .= "           <param name=\"rtp-ip\" value=\"\$\${local_ip_v4}\"/>\n";
                $xml .= "           <param name=\"sip-ip\" value=\"\$\${local_ip_v4}\"/>\n";
                $xml .= "           <param name=\"ext-rtp-ip\" value=\"auto-nat\"/>\n";
                $xml .= "           <param name=\"ext-sip-ip\" value=\"auto-nat\"/>\n";
                $xml .= "           <param name=\"rtp-timeout-sec\" value=\"300\"/>\n";
                $xml .= "           <param name=\"rtp-hold-timeout-sec\" value=\"1800\"/>\n";
                $xml .= "           <!--<param name=\"enable-3pcc\" value=\"true\"/>-->\n";
                $xml .= "           <param name=\"tls\" value=\"\$\${external_ssl_enable}\"/>\n";
                $xml .= "           <param name=\"tls-only\" value=\"false\"/>\n";
                $xml .= "           <param name=\"tls-bind-params\" value=\"transport=tls\"/>\n";
                $xml .= "           <param name=\"tls-sip-port\" value=\"\$\${external_tls_port}\"/>\n";
                $xml .= "           <!--<param name=\"tls-cert-dir\" value=\"\"/>-->\n";
                $xml .= "           <param name=\"tls-passphrase\" value=\"\"/>\n";
                $xml .= "           <!-- Verify the date on TLS certificates -->\n";
                $xml .= "           <param name=\"tls-verify-date\" value=\"true\"/>\n";
                $xml .= "           <param name=\"tls-verify-policy\" value=\"none\"/>\n";
                $xml .= "           <param name=\"tls-verify-depth\" value=\"2\"/>\n";
                $xml .= "           <param name=\"tls-verify-in-subjects\" value=\"\"/>\n";
                $xml .= "           <param name=\"tls-version\" value=\"\$\${sip_tls_version}\"/>\n";
                $xml .= "       </settings>\n";
                $xml .= "   </profile>\n";
                $xml .=     file_get_contents('etc_freeswitch/sip_profiles/internal.xml');
                $xml .= "   </profiles>\n";
                $xml .= "</configuration>\n";

            }elseif ($conf=='ivr.conf.xml'){

                $xml .= "<configuration name=\"ivr.conf\" description=\"IVR menus\">\n";
                $xml .= "<menus>\n";
                foreach (scandir("etc_freeswitch/ivr_menus") as $file){
                    if ($file=='.'||$file=='..') continue;
                    $xml .= file_get_contents('etc_freeswitch/ivr_menus/'.$file);
                }
                $xml .= "</menus>\n";
                $xml .= "</configuration>\n";

            }elseif ($conf=='dingaling.conf.xml'){

                $xml .= "<configuration name=\"dingaling.conf\" description=\"XMPP Jingle Endpoint\">\n";
                $xml .= "<settings>\n";
                $xml .= "<param name=\"debug\" value=\"0\"/>\n";
                $xml .= "<param name=\"codec-prefs\" value=\"H264,PCMU\"/>\n";
                $xml .= "</settings>\n";
                foreach (scandir("etc_freeswitch/jingle_profiles") as $file){
                    if ($file=='.'||$file=='..') continue;
                    $xml .= file_get_contents('etc_freeswitch/jingle_profiles/'.$file);
                }
                $xml .= "</configuration>\n";

            }elseif ($conf=='skinny.conf.xml'){

                $xml .= "<configuration name=\"skinny.conf\" description=\"Skinny Endpoints\">\n";
                $xml .= "<profiles>\n";
                foreach (scandir("etc_freeswitch/sip_profiles") as $file){
                    if ($file=='.'||$file=='..') continue;
                    $xml .= file_get_contents('etc_freeswitch/sip_profiles/'.$file);
                }
                $xml .= "</profiles>\n";
                $xml .= "</configuration>\n";

            }elseif ($conf=='unimrcp.conf.xml'){

                $xml .= "<configuration name=\"unimrcp.conf\" description=\"UniMRCP Client\">\n";
                $xml .= "<settings>\n";
                $xml .= "<param name=\"default-tts-profile\" value=\"voxeo-prophecy8.0-mrcp1\"/>\n";
                $xml .= "<param name=\"default-asr-profile\" value=\"voxeo-prophecy8.0-mrcp1\"/>\n";
                $xml .= "<param name=\"log-level\" value=\"DEBUG\"/>\n";
                $xml .= "<param name=\"enable-profile-events\" value=\"false\"/>\n";
                $xml .= "<param name=\"max-connection-count\" value=\"100\"/>\n";
                $xml .= "<param name=\"offer-new-connection\" value=\"1\"/>\n";
                $xml .= "<param name=\"request-timeout\" value=\"3000\"/>\n";
                $xml .= "</settings>\n";
                $xml .= "<profiles>\n";
                foreach (scandir("etc_freeswitch/mrcp_profiles") as $file){
                    if ($file=='.'||$file=='..') continue;
                    $xml .= file_get_contents('etc_freeswitch/mrcp_profiles/'.$file);
                }
                $xml .= "</profiles>\n";
                $xml .= "</configuration>\n";

            }else{
                $xml .= file_get_contents('etc_freeswitch/autoload_configs/'.$conf);
            }

        }
        $xml .= "</section>\n";
        $xml .= "</document>\n";
        return response($xml,200)->header("Content-type","text/xml");
    }

    /**
     * 拨打接口
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function dial(Request $request)
    {
        //验证数据
        $data = $request->all(['exten','phone']);
        if (!preg_match('/^\d{6,12}$/',$data['phone'])){
            return Response::json(['code'=>1,'msg'=>'被叫号码格式不正确']);
        }
        $sip = Sip::with(['merchant','gateway'])->where('username',$data['exten'])->first();
        if ($sip==null){
            return Response::json(['code'=>1,'msg'=>'分机号不存在']);
        }
        if ($sip->merchant==null || $sip->gateway==null){
            return Response::json(['code'=>1,'msg'=>'分机的商户网关信息异常']);
        }
        //验证商户信息

        //呼叫字符串
        $fs = new \Freeswitchesl();
        try{
            $fs->connect(config('freeswitch.event_socket.host'),config('freeswitch.event_socket.port'),config('freeswitch.event_socket.password'));
        }catch (\Exception $exception){
            Log::info('呼叫接口ESL连接异常：'.$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'无法连接服务器']);
        }
        $dialStr = "originate user/".$sip->username." gw".$sip->gateway->id."_";
        if ($sip->gateway->prefix){
            $dialStr .=$sip->gateway->prefix;
        }
        $dialStr .=$data["phone"]." XML default";
        try{
            $fs->bgapi($dialStr);
            return Response::json(['code'=>0,'msg'=>'呼叫成功']);
        }catch (\Exception $exception){
            Log::info("呼叫错误：".$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>'呼叫失败']);
        }

    }

    /**
     * 接收通话记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCdr(Request $request)
    {
        $uuid = $request->get('uuid');
        $cdrXml = $request->get('cdr');
        $cdrXml = "<?xml version=\"1.0\"?>
<cdr core-uuid=\"1eee52c9-73dd-4a6b-a13e-2bf391021578\" switchname=\"zhihong\">
  <channel_data>
    <state>CS_REPORTING</state>
    <direction>inbound</direction>
    <state_number>11</state_number>
    <flags>0=1;1=1;3=1;38=1;39=1;41=1;44=1;54=1;76=1;96=1;113=1;114=1;123=1;160=1;165=1</flags>
    <caps>1=1;2=1;3=1;4=1;5=1;6=1;8=1;9=1</caps>
  </channel_data>
  <call-stats>
    <audio>
      <inbound>
        <raw_bytes>0</raw_bytes>
        <media_bytes>0</media_bytes>
        <packet_count>0</packet_count>
        <media_packet_count>0</media_packet_count>
        <skip_packet_count>0</skip_packet_count>
        <jitter_packet_count>0</jitter_packet_count>
        <dtmf_packet_count>0</dtmf_packet_count>
        <cng_packet_count>0</cng_packet_count>
        <flush_packet_count>0</flush_packet_count>
        <largest_jb_size>0</largest_jb_size>
        <jitter_min_variance>0.00</jitter_min_variance>
        <jitter_max_variance>0.00</jitter_max_variance>
        <jitter_loss_rate>0.00</jitter_loss_rate>
        <jitter_burst_rate>0.00</jitter_burst_rate>
        <mean_interval>0.00</mean_interval>
        <flaw_total>0</flaw_total>
        <quality_percentage>100.00</quality_percentage>
        <mos>4.50</mos>
      </inbound>
      <outbound>
        <raw_bytes>0</raw_bytes>
        <media_bytes>0</media_bytes>
        <packet_count>0</packet_count>
        <media_packet_count>0</media_packet_count>
        <skip_packet_count>0</skip_packet_count>
        <dtmf_packet_count>0</dtmf_packet_count>
        <cng_packet_count>0</cng_packet_count>
        <rtcp_packet_count>0</rtcp_packet_count>
        <rtcp_octet_count>0</rtcp_octet_count>
      </outbound>
    </audio>
  </call-stats>
  <variables>
    <direction>inbound</direction>
    <uuid>47e84b8e-95b0-4d09-8339-9fb2a479e3fb</uuid>
    <session_id>10</session_id>
    <sip_from_user>8001</sip_from_user>
    <sip_from_uri>8001%4010.3.1.9</sip_from_uri>
    <sip_from_host>10.3.1.9</sip_from_host>
    <video_media_flow>disabled</video_media_flow>
    <text_media_flow>disabled</text_media_flow>
    <channel_name>sofia/internal/8001%4010.3.1.9</channel_name>
    <sip_local_network_addr>106.13.223.130</sip_local_network_addr>
    <sip_network_ip>172.16.0.6</sip_network_ip>
    <sip_network_port>55171</sip_network_port>
    <sip_invite_stamp>1574244029675105</sip_invite_stamp>
    <sip_received_ip>172.16.0.6</sip_received_ip>
    <sip_received_port>55171</sip_received_port>
    <sip_via_protocol>tcp</sip_via_protocol>
    <sip_authorized>true</sip_authorized>
    <Event-Name>REQUEST_PARAMS</Event-Name>
    <Core-UUID>1eee52c9-73dd-4a6b-a13e-2bf391021578</Core-UUID>
    <FreeSWITCH-Hostname>zhihong</FreeSWITCH-Hostname>
    <FreeSWITCH-Switchname>zhihong</FreeSWITCH-Switchname>
    <FreeSWITCH-IPv4>10.3.1.9</FreeSWITCH-IPv4>
    <FreeSWITCH-IPv6>%3A%3A1</FreeSWITCH-IPv6>
    <Event-Date-Local>2019-11-20%2018%3A00%3A29</Event-Date-Local>
    <Event-Date-GMT>Wed,%2020%20Nov%202019%2010%3A00%3A29%20GMT</Event-Date-GMT>
    <Event-Date-Timestamp>1574244029675105</Event-Date-Timestamp>
    <Event-Calling-File>sofia.c</Event-Calling-File>
    <Event-Calling-Function>sofia_handle_sip_i_invite</Event-Calling-Function>
    <Event-Calling-Line-Number>10316</Event-Calling-Line-Number>
    <Event-Sequence>2099</Event-Sequence>
    <sip_number_alias>8001</sip_number_alias>
    <sip_auth_username>8001</sip_auth_username>
    <sip_auth_realm>10.3.1.9</sip_auth_realm>
    <number_alias>8001</number_alias>
    <requested_user_name>8001</requested_user_name>
    <requested_domain_name>10.3.1.9</requested_domain_name>
    <toll_allow>domestic,international,local</toll_allow>
    <accountcode>8001</accountcode>
    <user_context>default</user_context>
    <effective_caller_id_name>8001</effective_caller_id_name>
    <effective_caller_id_number>8001</effective_caller_id_number>
    <outbound_caller_id_name>FreeSWITCH</outbound_caller_id_name>
    <outbound_caller_id_number>0000000000</outbound_caller_id_number>
    <user_name>8001</user_name>
    <domain_name>10.3.1.9</domain_name>
    <sip_from_user_stripped>8001</sip_from_user_stripped>
    <sofia_profile_name>internal</sofia_profile_name>
    <sofia_profile_url>sip%3Amod_sofia%40106.13.223.130%3A5060</sofia_profile_url>
    <recovery_profile_name>internal</recovery_profile_name>
    <sip_full_route>%3Csip%3A10.3.1.9%3Blr%3E</sip_full_route>
    <sip_recover_via>SIP/2.0/TCP%20172.16.0.6%3A55171%3Brport%3D55171%3Bbranch%3Dz9hG4bKPjc9fee3c37bf64d9c806c51d018167892%3Balias</sip_recover_via>
    <sip_allow>PRACK,%20INVITE,%20ACK,%20BYE,%20CANCEL,%20UPDATE,%20INFO,%20SUBSCRIBE,%20NOTIFY,%20REFER,%20MESSAGE,%20OPTIONS</sip_allow>
    <sip_req_user>87452174</sip_req_user>
    <sip_req_uri>87452174%4010.3.1.9</sip_req_uri>
    <sip_req_host>10.3.1.9</sip_req_host>
    <sip_to_user>87452174</sip_to_user>
    <sip_to_uri>87452174%4010.3.1.9</sip_to_uri>
    <sip_to_host>10.3.1.9</sip_to_host>
    <sip_contact_params>ob</sip_contact_params>
    <sip_contact_user>8001</sip_contact_user>
    <sip_contact_port>60947</sip_contact_port>
    <sip_contact_uri>8001%40172.16.0.6%3A60947</sip_contact_uri>
    <sip_contact_host>172.16.0.6</sip_contact_host>
    <sip_user_agent>MicroSIP/3.19.21</sip_user_agent>
    <sip_via_host>172.16.0.6</sip_via_host>
    <sip_via_port>55171</sip_via_port>
    <sip_via_rport>55171</sip_via_rport>
    <max_forwards>70</max_forwards>
    <presence_id>8001%4010.3.1.9</presence_id>
    <switch_r_sdp>v%3D0%0D%0Ao%3D-%203783261537%203783261537%20IN%20IP4%20172.16.0.6%0D%0As%3Dpjmedia%0D%0Ab%3DAS%3A84%0D%0At%3D0%200%0D%0Aa%3DX-nat%3A0%0D%0Am%3Daudio%204018%20RTP/AVP%209%208%200%204%20107%2018%20101%0D%0Ac%3DIN%20IP4%20172.16.0.6%0D%0Ab%3DTIAS%3A64000%0D%0Aa%3Drtpmap%3A9%20G722/8000%0D%0Aa%3Drtpmap%3A8%20PCMA/8000%0D%0Aa%3Drtpmap%3A0%20PCMU/8000%0D%0Aa%3Drtpmap%3A4%20G723/8000%0D%0Aa%3Drtpmap%3A107%20opus/48000/2%0D%0Aa%3Dfmtp%3A107%20maxplaybackrate%3D24000%3Bsprop-maxcapturerate%3D24000%3Bmaxaveragebitrate%3D20000%3Buseinbandfec%3D1%0D%0Aa%3Drtpmap%3A18%20G729/8000%0D%0Aa%3Drtpmap%3A101%20telephone-event/8000%0D%0Aa%3Dfmtp%3A101%200-16%0D%0Aa%3Drtcp%3A4019%20IN%20IP4%20172.16.0.6%0D%0Aa%3Dssrc%3A2119985223%20cname%3A012779ae3fa67506%0D%0A</switch_r_sdp>
    <ep_codec_string>mod_spandsp.G722%408000h%4020i%4064000b,CORE_PCM_MODULE.PCMA%408000h%4020i%4064000b,CORE_PCM_MODULE.PCMU%408000h%4020i%4064000b,mod_opus.opus%4048000h%4020i%402c</ep_codec_string>
    <DP_MATCH>87452174</DP_MATCH>
    <DP_MATCH>87452174</DP_MATCH>
    <call_uuid>47e84b8e-95b0-4d09-8339-9fb2a479e3fb</call_uuid>
    <rtp_use_codec_string>G722,OPUS,PCMU,PCMA,VP8</rtp_use_codec_string>
    <remote_audio_media_flow>sendrecv</remote_audio_media_flow>
    <audio_media_flow>sendrecv</audio_media_flow>
    <rtp_remote_audio_rtcp_port>4019</rtp_remote_audio_rtcp_port>
    <rtp_audio_recv_pt>9</rtp_audio_recv_pt>
    <rtp_use_codec_name>G722</rtp_use_codec_name>
    <rtp_use_codec_rate>8000</rtp_use_codec_rate>
    <rtp_use_codec_ptime>20</rtp_use_codec_ptime>
    <rtp_use_codec_channels>1</rtp_use_codec_channels>
    <rtp_last_audio_codec_string>G722%408000h%4020i%401c</rtp_last_audio_codec_string>
    <read_codec>G722</read_codec>
    <original_read_codec>G722</original_read_codec>
    <read_rate>16000</read_rate>
    <original_read_rate>16000</original_read_rate>
    <write_codec>G722</write_codec>
    <write_rate>16000</write_rate>
    <dtmf_type>rfc2833</dtmf_type>
    <local_media_ip>10.3.1.9</local_media_ip>
    <local_media_port>17548</local_media_port>
    <advertised_media_ip>106.13.223.130</advertised_media_ip>
    <rtp_use_timer_name>soft</rtp_use_timer_name>
    <rtp_use_pt>9</rtp_use_pt>
    <rtp_use_ssrc>3587833989</rtp_use_ssrc>
    <rtp_2833_send_payload>101</rtp_2833_send_payload>
    <rtp_2833_recv_payload>101</rtp_2833_recv_payload>
    <remote_media_ip>172.16.0.6</remote_media_ip>
    <remote_media_port>4018</remote_media_port>
    <rtp_local_sdp_str>v%3D0%0D%0Ao%3DFreeSWITCH%201574226482%201574226483%20IN%20IP4%20106.13.223.130%0D%0As%3DFreeSWITCH%0D%0Ac%3DIN%20IP4%20106.13.223.130%0D%0At%3D0%200%0D%0Am%3Daudio%2017548%20RTP/AVP%209%20101%0D%0Aa%3Drtpmap%3A9%20G722/8000%0D%0Aa%3Drtpmap%3A101%20telephone-event/8000%0D%0Aa%3Dfmtp%3A101%200-16%0D%0Aa%3Dptime%3A20%0D%0Aa%3Dsendrecv%0D%0Aa%3Drtcp%3A17549%20IN%20IP4%20106.13.223.130%0D%0A</rtp_local_sdp_str>
    <endpoint_disposition>ANSWER</endpoint_disposition>
    <hangup_after_bridge>true</hangup_after_bridge>
    <current_application_data>user/87452174</current_application_data>
    <current_application>bridge</current_application>
    <bypass_media_after_bridge>true</bypass_media_after_bridge>
    <originate_disposition>SUBSCRIBER_ABSENT</originate_disposition>
    <DIALSTATUS>SUBSCRIBER_ABSENT</DIALSTATUS>
    <sip_to_tag>UvF498aBZZ3cm</sip_to_tag>
    <sip_from_tag>87bdbba8a2df4bb0b41c6a0d1a9aa87f</sip_from_tag>
    <sip_cseq>28950</sip_cseq>
    <sip_call_id>6c788d93a99e462eadd76ca4b03d755f</sip_call_id>
    <sip_full_via>SIP/2.0/UDP%20172.16.0.6%3A60947%3Brport%3D32656%3Bbranch%3Dz9hG4bKPj1cd5877673944f65b345899e7f2d43e0%3Breceived%3D182.139.182.86</sip_full_via>
    <sip_from_display>8001</sip_from_display>
    <sip_full_from>%228001%22%20%3Csip%3A8001%4010.3.1.9%3E%3Btag%3D87bdbba8a2df4bb0b41c6a0d1a9aa87f</sip_full_from>
    <sip_full_to>%3Csip%3A87452174%4010.3.1.9%3E%3Btag%3DUvF498aBZZ3cm</sip_full_to>
    <originate_failed_cause>SUBSCRIBER_ABSENT</originate_failed_cause>
    <hangup_cause>SUBSCRIBER_ABSENT</hangup_cause>
    <hangup_cause_q850>20</hangup_cause_q850>
    <digits_dialed>none</digits_dialed>
    <start_stamp>2019-11-20%2018%3A00%3A29</start_stamp>
    <profile_start_stamp>2019-11-20%2018%3A00%3A29</profile_start_stamp>
    <answer_stamp>2019-11-20%2018%3A00%3A30</answer_stamp>
    <progress_media_stamp>2019-11-20%2018%3A00%3A30</progress_media_stamp>
    <end_stamp>2019-11-20%2018%3A00%3A30</end_stamp>
    <start_epoch>1574244029</start_epoch>
    <start_uepoch>1574244029875129</start_uepoch>
    <profile_start_epoch>1574244029</profile_start_epoch>
    <profile_start_uepoch>1574244029875129</profile_start_uepoch>
    <answer_epoch>1574244030</answer_epoch>
    <answer_uepoch>1574244030095179</answer_uepoch>
    <bridge_epoch>0</bridge_epoch>
    <bridge_uepoch>0</bridge_uepoch>
    <last_hold_epoch>0</last_hold_epoch>
    <last_hold_uepoch>0</last_hold_uepoch>
    <hold_accum_seconds>0</hold_accum_seconds>
    <hold_accum_usec>0</hold_accum_usec>
    <hold_accum_ms>0</hold_accum_ms>
    <resurrect_epoch>0</resurrect_epoch>
    <resurrect_uepoch>0</resurrect_uepoch>
    <progress_epoch>0</progress_epoch>
    <progress_uepoch>0</progress_uepoch>
    <progress_media_epoch>1574244030</progress_media_epoch>
    <progress_media_uepoch>1574244030095179</progress_media_uepoch>
    <end_epoch>1574244030</end_epoch>
    <end_uepoch>1574244030235115</end_uepoch>
    <last_app>bridge</last_app>
    <last_arg>user/87452174</last_arg>
    <caller_id>%228001%22%20%3C8001%3E</caller_id>
    <duration>1</duration>
    <billsec>0</billsec>
    <progresssec>0</progresssec>
    <answersec>1</answersec>
    <waitsec>0</waitsec>
    <progress_mediasec>1</progress_mediasec>
    <flow_billsec>1</flow_billsec>
    <mduration>360</mduration>
    <billmsec>140</billmsec>
    <progressmsec>0</progressmsec>
    <answermsec>220</answermsec>
    <waitmsec>0</waitmsec>
    <progress_mediamsec>220</progress_mediamsec>
    <flow_billmsec>360</flow_billmsec>
    <uduration>359986</uduration>
    <billusec>139936</billusec>
    <progressusec>0</progressusec>
    <answerusec>220050</answerusec>
    <waitusec>0</waitusec>
    <progress_mediausec>220050</progress_mediausec>
    <flow_billusec>359986</flow_billusec>
    <sip_hangup_disposition>send_bye</sip_hangup_disposition>
    <rtp_audio_in_raw_bytes>0</rtp_audio_in_raw_bytes>
    <rtp_audio_in_media_bytes>0</rtp_audio_in_media_bytes>
    <rtp_audio_in_packet_count>0</rtp_audio_in_packet_count>
    <rtp_audio_in_media_packet_count>0</rtp_audio_in_media_packet_count>
    <rtp_audio_in_skip_packet_count>0</rtp_audio_in_skip_packet_count>
    <rtp_audio_in_jitter_packet_count>0</rtp_audio_in_jitter_packet_count>
    <rtp_audio_in_dtmf_packet_count>0</rtp_audio_in_dtmf_packet_count>
    <rtp_audio_in_cng_packet_count>0</rtp_audio_in_cng_packet_count>
    <rtp_audio_in_flush_packet_count>0</rtp_audio_in_flush_packet_count>
    <rtp_audio_in_largest_jb_size>0</rtp_audio_in_largest_jb_size>
    <rtp_audio_in_jitter_min_variance>0.00</rtp_audio_in_jitter_min_variance>
    <rtp_audio_in_jitter_max_variance>0.00</rtp_audio_in_jitter_max_variance>
    <rtp_audio_in_jitter_loss_rate>0.00</rtp_audio_in_jitter_loss_rate>
    <rtp_audio_in_jitter_burst_rate>0.00</rtp_audio_in_jitter_burst_rate>
    <rtp_audio_in_mean_interval>0.00</rtp_audio_in_mean_interval>
    <rtp_audio_in_flaw_total>0</rtp_audio_in_flaw_total>
    <rtp_audio_in_quality_percentage>100.00</rtp_audio_in_quality_percentage>
    <rtp_audio_in_mos>4.50</rtp_audio_in_mos>
    <rtp_audio_out_raw_bytes>0</rtp_audio_out_raw_bytes>
    <rtp_audio_out_media_bytes>0</rtp_audio_out_media_bytes>
    <rtp_audio_out_packet_count>0</rtp_audio_out_packet_count>
    <rtp_audio_out_media_packet_count>0</rtp_audio_out_media_packet_count>
    <rtp_audio_out_skip_packet_count>0</rtp_audio_out_skip_packet_count>
    <rtp_audio_out_dtmf_packet_count>0</rtp_audio_out_dtmf_packet_count>
    <rtp_audio_out_cng_packet_count>0</rtp_audio_out_cng_packet_count>
    <rtp_audio_rtcp_packet_count>0</rtp_audio_rtcp_packet_count>
    <rtp_audio_rtcp_octet_count>0</rtp_audio_rtcp_octet_count>
  </variables>
  <app_log>
    <application app_name=\"answer\" app_data=\"\" app_stamp=\"1574244030098439\"></application>
    <application app_name=\"set\" app_data=\"bypass_media=true\" app_stamp=\"1574244030104927\"></application>
    <application app_name=\"set\" app_data=\"hangup_after_bridge=true\" app_stamp=\"1574244030105234\"></application>
    <application app_name=\"bridge\" app_data=\"user/87452174\" app_stamp=\"1574244030105488\"></application>
  </app_log>
  <callflow dialplan=\"XML\" unique-id=\"67bed4aa-6970-48dc-9661-b156bf5d94d7\" profile_index=\"1\">
    <extension name=\"Local_Extension\" number=\"87452174\">
      <application app_name=\"answer\" app_data=\"\"></application>
      <application app_name=\"set\" app_data=\"bypass_media=true\"></application>
      <application app_name=\"set\" app_data=\"hangup_after_bridge=true\"></application>
      <application app_name=\"bridge\" app_data=\"user/87452174\"></application>
    </extension>
    <caller_profile>
      <username>8001</username>
      <dialplan>XML</dialplan>
      <caller_id_name>8001</caller_id_name>
      <caller_id_number>8001</caller_id_number>
      <callee_id_name></callee_id_name>
      <callee_id_number></callee_id_number>
      <ani>8001</ani>
      <aniii></aniii>
      <network_addr>172.16.0.6</network_addr>
      <rdnis></rdnis>
      <destination_number>87452174</destination_number>
      <uuid>47e84b8e-95b0-4d09-8339-9fb2a479e3fb</uuid>
      <source>mod_sofia</source>
      <context>default</context>
      <chan_name>sofia/internal/8001@10.3.1.9</chan_name>
    </caller_profile>
    <times>
      <created_time>1574244029875129</created_time>
      <profile_created_time>1574244029875129</profile_created_time>
      <progress_time>0</progress_time>
      <progress_media_time>1574244030095179</progress_media_time>
      <answered_time>1574244030095179</answered_time>
      <bridged_time>0</bridged_time>
      <last_hold_time>0</last_hold_time>
      <hold_accum_time>0</hold_accum_time>
      <hangup_time>1574244030235115</hangup_time>
      <resurrect_time>0</resurrect_time>
      <transfer_time>0</transfer_time>
    </times>
  </callflow>
</cdr>";
        try{
            $objectxml = simplexml_load_string($cdrXml);
            $cdrData = json_decode(json_encode($objectxml),true);
        }catch (\Exception $exception){
            Log::info("解析通话记录xml格式异常：".$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>$exception->getMessage()]);
        }

        try{
            $data = [
                'uuid' => $uuid,
                'duration' => $cdrData['variables']['duration'],
                'billsec' => $cdrData['variables']['billsec'],
                'start_at' => $cdrData['variables']['start_stamp']?urldecode($cdrData['variables']['start_stamp']):null,
                'answer_at' => $cdrData['variables']['answer_stamp']?urldecode($cdrData['variables']['answer_stamp']):null,
                'end_at' => $cdrData['variables']['end_stamp']?urldecode($cdrData['variables']['end_stamp']):null,
                'record_file' => $cdrData['variables']['record_file']??null,
                'user_data' => $cdrData['variables']['user_data']??null,
                'direction' => 1,
                'hangup_cause' => $cdrData['variables']['hangup_cause'],
            ];
            if (isset($cdrData['variables']['user_name'])){
                $data['src'] = $cdrData['variables']['user_name'];
                $data['dst'] = $cdrData['callflow']['caller_profile']['destination_number'];
            }else{
                $data['src'] = $cdrData['variables']['dialed_user'];
                $data['dst'] = $cdrData['callflow'][0]['caller_profile']['origination']['origination_caller_profile']['destination_number'];
            }
            Cdr::create($data);
            return Response::json(['code'=>0,'msg'=>'ok']);
        }catch (\Exception $exception){
            Log::info("通话记录写入库异常：".$exception->getMessage());
            return Response::json(['code'=>1,'msg'=>$exception->getMessage()]);
        }
    }

}
