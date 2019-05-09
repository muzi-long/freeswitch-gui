<?php

namespace App\Http\Controllers;

use App\Models\Extension;
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
        $username = $request->get('user');
        $sip = Sip::where('username', $username)->first();
        if ($sip==null) {
            $xml = <<< XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<document type="freeswitch/xml">
  <section name="directory">
  </section>
</document>
XML;
            return response($xml,404)->header("Content-type","text/xml");
        }
        $outbound_caller_id_number = $sip->outbound_caller_id_number??"\$\${outbound_caller_id}";
        $xml = <<< XML
<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<document type="freeswitch/xml">
  <section name="directory">
    <domain name="\$\${domain}">
      <params>
        <param name="dial-string" value="{presence_id=\${dialed_user}@\${dialed_domain}}\${sofia_contact(\${dialed_user}@\${dialed_domain})}"/>
      </params>
      <groups>
        <group name="default">
          <users>
            <user id="{$sip->username}">
              <params>
                <param name="password" value="{$sip->password}"/>
                <param name="vm-password" value="{$sip->password}"/>
              </params>
              <variables>
                <variable name="toll_allow" value="domestic,international,local"/>
                <variable name="accountcode" value="{$sip->username}"/>
                <variable name="user_context" value="{$sip->context}"/>
                <variable name="effective_caller_id_name" value="{$sip->effective_caller_id_name}"/>
                <variable name="effective_caller_id_number" value="{$sip->effective_caller_id_number}"/>
                <!-- <variable name="outbound_caller_id_name" value="\$\${outbound_caller_name}"/> -->
                <variable name="outbound_caller_id_number" value="{$outbound_caller_id_number}"/>
                <variable name="callgroup" value="{$sip->callgroup}"/>
              </variables>
            </user>
          </users>
        </group>
      </groups>
    </domain>
  </section>
</document>
XML;
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
