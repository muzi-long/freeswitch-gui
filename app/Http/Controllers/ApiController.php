<?php

namespace App\Http\Controllers;

use App\Models\Extension;
use App\Models\Group;
use App\Models\Sip;
use Illuminate\Http\Request;
use Log;

class ApiController extends Controller
{
    /**
     * 分机动态注册
     * @param Request $request
     * @return bool
     */
    public function directory(Request $request)
    {
        $sips = Sip::get();
        $groups = Group::with('sips')->whereHas('sips')->get();

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
        foreach ($groups as $group){
            $xml .= "<group name=\"".$group->name."\">\n";
            $xml .= "    <users>\n";
            foreach ($group->sips as $sip){
                $xml .= "   <user id=\"".$sip->username."\" type=\"pointer\"/>";
            }
            $xml .= "    </users>\n";
            $xml .= "</group>\n";
        }

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
    
}
