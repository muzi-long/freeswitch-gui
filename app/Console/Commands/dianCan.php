<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class dianCan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diancan:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    public $client = null;
    public $users = [];
    public $areaId = 'CDBL6';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
        $this->users = [
            [
                'data' => '8wZXa15s1isZrUk7tQnfbYSGgCUQuiYb4735f6rlO50oN6YqEvrdsvyqfWri\/P29ch27hGlE6z1kVkuGD+oRPoX0vxu2MmNq9wzarbn4TDDCJuQ20S4nsK5dWK4WhW0iud9Yq3Ftv6eEICHy2qcwRP7gEdHqoyQTxkfwIb3Uvn1TA3OqHq3vgbxJ7YqDqcP8tF9sGfn6JJ8mVSI+pZygtLLeqfdVxXO1IQoe8NSAj00=',
                'jobNum' => '6303158',
            ],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->addDinner();
    }

    public function getToken($user)
    {
        try {
            $response = $this->client->post('http://guazhai.dgg188.cn:8088/mobile/userlogin', [
                'body' => '{"data":"'.$user["data"].'"}',
                'headers' => [
                    'Content-Type' => 'application/json'
                ]
            ]);
            $res = json_decode($response->getBody(), true);
            if ($res['code'] == 0) {
                return $res['data']['token'];
            }
        } catch (\Exception $exception) {
            return false;
        }
        return false;
    }

    public function getMealId($token)
    {
        try {
            $response = $this->client->post('http://cloudfront.dgg188.cn/cloud-front/dinner/getUsableMealList', [
                'json' => [
                    'haveMealCode' => $this->areaId,
                ],
                'headers' => [
                    'token' => $token,
                    'Content-Type' => 'application/json'
                ]
            ]);
            $res = json_decode($response->getBody(), true);
            if ($res['code'] == 0) {
                foreach ($res['data'] as $item) {
                    if ($item['mealTypeName']=='午餐' && $item['mealName'] != '白米饭' && $item['foodName'] != '白米饭' && strpos($item['mealName'],'荤')!==false) {
                        return $item['id'];
                    }
                }
            }
        } catch (\Exception $exception) {
            return false;
        }
        return false;
    }

    public function sendWechatMessage($to, $msg)
    {
        try {
            $this->client->post('https://wechat.dgg188.cn/corpwechat/sendtext', [
                'json' => [
                    "touser" => $to,
                    "text" => [
                        "content" => $msg
                    ],
                    "sendName" => "",
                    "appName" => ""
                ],
                'verify' => false
            ]);
        } catch (\Exception $exception) {
            return false;
        }
        return false;
    }

    public function addDinner()
    {
        foreach ($this->users as $user){
            $token = $this->getToken($user);
            if (!$token){
                $this->sendWechatMessage($user['jobNum'],'获取token失败');
                continue;
            }
            $mealId = $this->getMealId($token);
            if (!$mealId){
                $this->sendWechatMessage($user['jobNum'],'获取套餐失败');
                continue;
            }
            try {
                $this->client->post('http://cloudfront.dgg188.cn/cloud-front/dinner/addDinnerMeal', [
                    'headers' => [
                        'token' => $token,
                        'Content-Type' => 'application/json'
                    ],
                    'json' => [
                        'mealId' => $mealId,
                        'areaId' => $this->areaId,
                    ]
                ]);
            } catch (\Exception $exception) {
                continue;
            }
        }
    }
}
