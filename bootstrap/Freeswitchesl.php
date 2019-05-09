<?php

class Freeswitchesl {

    public function __construct() {
        $this->socket = "";
        $this->sorts = "";
        $this->length = 1024;
    }

    public function eliminate($parameter)
    {
        $array = array(" ","　","\t","\n","\r");
        return str_replace($array, '', $parameter);
    }

    public function eliminateLine($parameter)
    {
        return str_replace("\n\n", "\n", $parameter);
    }

    public function typeClear($response)
    {
        $commenType = array("Content-Type: text/event-xml\n","Content-Type: text/event-plain\n","Content-Type: text/event-json\n");
        return str_replace($commenType, '', $response);
    }

    public function connect($host,$port,$password)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $connection = socket_connect($this->socket, $host,$port);
        $connect = false;
        $error = "";
        while ($socket_info = @socket_read($this->socket, 1024, PHP_NORMAL_READ)) { 
            $eliminate_socket_info = $this->eliminate($socket_info);
            if ($eliminate_socket_info == "Content-Type:auth/request") {
                socket_write($this->socket, "auth ".$password."\r\n\r\n");
            }elseif ($eliminate_socket_info == "") {
                continue;
            }elseif ($eliminate_socket_info == "Content-Type:command/reply") {
                continue;
            }elseif ($eliminate_socket_info == "Reply-Text:+OKaccepted") {
                $connect = true;
                break;
            } else {
                $error .= $eliminate_socket_info."\r\n";
            }
        }
        if (!$connect) {
            echo $error;
        }
        return $connect;
    }

    public function api($api,$args="")
    {
        if ($this->socket) {
            socket_write($this->socket, "api ".$api." ".$args."\r\n\r\n");
        }
        $response = $this->recvEvent("common");
        return $response;
    }

    public function bgapi($api,$args="",$custom_job_uuid="")
    {
        if ($this->socket) {
            socket_write($this->socket, "bgapi ".$api." ".$args." ".$custom_job_uuid."\r\n\r\n");
        }
        return "executed";
    }

    public function execute($app,$args,$uuid)
    {
        if ($this->socket) {
            $str = "sendmsg ".$uuid."\ncall-command: execute\nexecute-app-name: ".$app."\nexecute-app-arg: ".$args."\n\n";
            socket_write($this->socket, $str);
        }
        $response = $this->recvEvent("common");
        return $response;
    }

    public function executeAsync($app,$args,$uuid)
    {
        if ($this->socket) {
            $str = "sendmsg ".$uuid."\ncall-command: executeAsync\nexecute-app-name: ".$app."\nexecute-app-arg: ".$args."\n\n";
            socket_write($this->socket, $str);
        }
        return "executed";
    }

    public function sendmsg($uuid)
    {
        if ($this->socket) {
            socket_write($this->socket, "sendmsg ".$uuid."\r\n\r\n");
        }
        return "executed";
    }

    public function events($sorts,$args)
    {
        $this->sorts = $sorts;
        if ($sorts == "json") {
            $sorts = "xml";
        }
        if ($this->socket) {
            socket_write($this->socket, "event ".$sorts." ".$args."\r\n\r\n");
        }
        return true;
    }

    public function getHeader($response,$args)
    {
        $serialize = $this->serialize($response,"json");
        $serializearray = json_decode($serialize);
        try {
            return $serializearray->$args;
        } catch (Exception $e) {
            return "";
        }
    }

    public function recvEvent($type="event")
    {
        $response = '';
        $length = 0;
        $x = 0;
        while ($socket_info = @socket_read($this->socket, 1024, PHP_NORMAL_READ)){ 
            $x++;
            usleep(100);
            if ($length > 0) {
                $response .= $socket_info;
            }
            if ($length == 0 && strpos($socket_info, 'Content-Length:') !== false) {
                $lengtharray = explode("Content-Length:",$socket_info);
                if ($type == "event") {
                    $length = (int)$lengtharray[1]+30;
                } else {
                    $length = (int)$lengtharray[1];
                }
            }

            if ($length > 0 && strlen($response) >= $length) {
                break;
            }

            if ($x > 10000) break;
        }


        if ($this->sorts == "json" && $type == "event") {
            $response = $this->typeClear($response);
            $responsedata = simplexml_load_string($response);
            $response = [];
            foreach ($responsedata->headers->children() as $key => $value) {
                $response[(string)$key] = (string)$value;
            }
            return json_encode($response);
        } else {
            $response = $this->eliminateLine($response);
        }
        return $response;
    }

    public function serialize($response,$type)
    {
        $response = $this->typeClear($response);
        if ($this->sorts == $type) return $response;
        if ($this->sorts == "json") {
            $responsedata = json_decode($response);
            if ($type == "plain") {
                $response = "";
                foreach ($responsedata as $key => $value) {
                    $responseline = $key.": ".$value."\r\n";
                    $response .= $responseline;
                }
            } else {
                $response = "<event>\r\n  <headers>\r\n";
                foreach ($responsedata as $key => $value) {
                    $responseline = "    <".$key.">".$value."</".$key.">"."\r\n";
                    $response .= $responseline;
                }
                $response .= "  </headers>\r\n</event>";
            }
            return $response;
        } elseif ($this->sorts == "xml") {
            $responsedata = simplexml_load_string($response);
            if ($type == "plain") {
                $response = "";
                foreach ($responsedata->headers->children() as $key => $value) {
                    $responseline = (string)$key.": ".(string)$value."\r\n";
                    $response .= $responseline;
                }
                return $response;
            } else {
                $response = [];
                foreach ($responsedata->headers->children() as $key => $value) {
                    $response[(string)$key] = (string)$value;
                }
                return json_encode($response);
            }
        } else {
            $response = str_replace("\n", '","', $response);
            $response = str_replace(": ", '":"', $response);
            $response = substr($response, 0, -2);
            $response = '{"'.$response.'}';
            if ($type == "json") return $response;
            $responsedata = json_decode($response);
            $response = "<event>\r\n  <headers>\r\n";
            foreach ($responsedata as $key => $value) {
                $responseline = "    <".$key.">".$value."</".$key.">"."\r\n";
                $response .= $responseline;
            }
            $response .= "  </headers>\r\n</event>";
            return $response;
        }
    }

    public function disconnect()
    {
        socket_close($this->socket); 
    }
}

