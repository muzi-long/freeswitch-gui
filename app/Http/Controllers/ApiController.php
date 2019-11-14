<?php

namespace App\Http\Controllers;

use App\Models\Extension;
use App\Models\Gateway;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
        $sips = Sip::get();
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

}
