<?php

namespace App\Service;

use \Swoole\Coroutine\Socket;

/**
 * Class SwooleFreeswitch
 * @package App\Service
 */
class SwooleFreeswitch
{

    /**
     * @var Socket | null
     */
    protected $socket = null;

    /**
     * @var int
     */
    protected $length = 0;

    public function __construct()
    {
        $this->socket = new Socket(AF_INET, SOCK_STREAM, SOL_TCP);

        $this->socket->setProtocol(['open_eof_check' => true, 'package_eof' => "\n"]);
    }

    /**
     * 连接fs并认证
     * @param string $host
     * @param string $port
     * @param string|null $password
     * @return bool
     */
    public function connect(?string $host = null, ?string $port = null, ?string $password = null)
    {
        $host = $host ?? config('freeswitch.esl.host');
        $port = $port ?? config('freeswitch.esl.port');
        $password = $password ?? config('freeswitch.esl.password');
        $res = $this->socket->connect($host, $port);
        if (!$res) return false;
        $this->socket->bind($host, $port);
        $socketInfoFirst = $this->socket->recv();
        $socketInfoFirst = $this->eliminate($socketInfoFirst);
        if ($socketInfoFirst == 'Content-Type:auth/request') {
            $this->socket->send("auth " . $password . "\r\n\r\n");
        }
        $socketInfoSecond = $this->socket->recv();
        $socketInfoSecond = $this->typeClear($socketInfoSecond);
        $socketInfoSecond = trim($socketInfoSecond);
        if ($socketInfoSecond == "Reply-Text: +OK accepted") return true;
        return false;
    }

    public function eliminate($parameter)
    {
        $array = array(" ", "　", "\t", "\n", "\r");
        return str_replace($array, '', $parameter);
    }

    public function eliminateLine($parameter)
    {
        return str_replace("\n\n", "\n", $parameter);
    }

    /**
     * 清除内容类型
     * @param $response
     * @return string|string[]
     */
    public function typeClear($response)
    {
        $commenType = [
            "Content-Type: text/event-xml\n",
            "Content-Type: text/event-plain\n",
            "Content-Type: text/event-json\n",
            "Content-Type: api/response\n",
            "Content-Type: command/reply\n",
        ];
        return str_replace($commenType, '', $response);
    }

    /**
     * 发送API命令
     * @param $api
     * @param string $args
     * @return false|string|string[]
     */
    public function api($api, $args = "")
    {
        if ($this->socket) {
            $this->socket->send("api " . $api . " " . $args . "\r\n\r\n");
        }
        return $this->recvEvent("common");
    }

    /**
     * 异步发送API命令
     * @param $api
     * @param string $args
     * @param string $custom_job_uuid
     * @return null
     */
    public function bgapi($api, $args = "", $custom_job_uuid = "")
    {
        if ($this->socket) {
            $this->socket->send("bgapi " . $api . " " . $args . " " . $custom_job_uuid . "\r\n\r\n");
        }
        return $custom_job_uuid;
    }

    /**
     * 执行app
     * @param $app
     * @param $args
     * @param $uuid
     * @return false|string|string[]
     */
    public function execute($app, $args, $uuid)
    {
        if ($this->socket) {
            $str = "sendmsg " . $uuid . "\ncall-command: execute\nexecute-app-name: " . $app . "\nexecute-app-arg: " . $args . "\n\n";
            $this->socket->send($str);
        }
        return $this->recvEvent();
    }

    /**
     * 异步执行app
     * @param $app
     * @param $args
     * @param $uuid
     * @return null
     */
    public function executeAsync($app, $args, $uuid)
    {
        if ($this->socket) {
            $str = "sendmsg " . $uuid . "\ncall-command: executeAsync\nexecute-app-name: " . $app . "\nexecute-app-arg: " . $args . "\n\n";
            $this->socket->send($str);
        }
        return null;
    }

    /**
     * 发送消息
     * @param $uuid
     * @return null
     */
    public function sendmsg($uuid)
    {
        if ($this->socket) {
            $this->socket->send("sendmsg " . $uuid . "\r\n\r\n");
        }
        return null;
    }

    /**
     * 设置需要监听的事件，默认监听所有
     * @param string $eventnames
     * @return null
     */
    public function events($eventnames = "ALL")
    {
        if ($this->socket) {
            $this->socket->send("event plain " . $eventnames . "\r\n\r\n");
        }
        return null;
    }

    /**
     * @param string $type
     * @return string
     */
    public function recvEvent($type = "event")
    {
        $response = "";
        $x = 0;
        $length = 0;
        while (true) {
            $x++;
            $socketInfo = $this->socket->recvPacket();
            if ($length > 0) {
                $response .= $socketInfo;
            }
            if (strpos($socketInfo, 'Content-Length:') !== false) {
                $lengtharray = explode("Content-Length:", $socketInfo);
                if ($type == "event") {
                    $length = (int)$lengtharray[1] + 30;
                } else {
                    $length = (int)$lengtharray[1];
                }
            }
            if ($length > 0 && strlen($response) >= $length) {
                break;
            }
            if ($x > 10000) break;
        }
        if ($type == "event") {
            $response = $this->typeClear($response);
        } else {
            $response = $this->eliminateLine($response);
        }
        return trim($response);
    }

    /**
     * 解析数据
     * @param $response
     * @return mixed
     */
    public function serialize($response)
    {
        $res = [];
        if (!$response) return $res;
        try {
            $arr1 = explode("\n", $response);
            foreach ($arr1 as $line) {
                if (strpos($line, ":") !== false) {
                    $item = explode(":", $line);
                    $res[$item[0]] = trim(urldecode($item[1]));
                }
            }
        } catch (\Exception $exception) {
            print_r("esl解析事件数据异常：" . $exception->getMessage() . "\n");
        }
        return $res;
    }

    /**
     * 过滤uuid
     * @param $uuid
     * @return null
     */
    public function filteruuid($uuid)
    {
        if ($this->socket) {
            $this->socket->send("filter Unique-ID " . $uuid . "\r\n\r\n");
        }
        return null;
    }

    /**
     * 断开连接
     */
    public function disconnect()
    {
        if ($this->socket) {
            $this->socket->send("exit" . "\r\n\r\n");
            $this->socket->close();
            $this->socket = null;
        }
    }

    /**
     * 广播
     * @param string $uuid
     * @param string $audio_path
     * @param string $idx 比如aleg或bleg等
     */
    public function uuidBroadcast(string $uuid, string $audio_path, string $idx = '')
    {
        $this->bgapi("uuid_broadcast", sprintf("%s %s %s", $uuid, $audio_path, $idx));
    }

    /**
     * 结束广播
     * @param string $uuid
     */
    public function uuidBreak(string $uuid)
    {
        $this->bgapi("uuid_break", sprintf("%s all", $uuid)); // 结束音乐播放
    }
}

