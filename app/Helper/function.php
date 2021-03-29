<?php


use GuzzleHttp\Exception\GuzzleException;

if (!function_exists('uuid_generate')) {
    /**
     * 生成唯一不重复的uuid
     * @return string
     */
    function uuid_generate()
    {
        return md5(\Illuminate\Support\Facades\Redis::incr('uuid_generate_id') . uniqid(mt_rand(), true));
    }
}

if (!function_exists('create_customer_num')) {
    /**
     * 生成客户编号
     * @return string
     */
    function create_customer_num()
    {
        return 'K'.date('YmdHis').\Illuminate\Support\Facades\Redis::incr('customer_num_id');
    }
}

if (!function_exists('create_order_num')) {
    /**
     * 生成唯一订单号
     * @return string
     */
    function create_order_num()
    {
        return 'D'.date('YmdHis').\Illuminate\Support\Facades\Redis::incr('order_num_id');
    }
}

if (!function_exists('recursive')) {
    /**
     * 递归树形上下级
     * @param $data
     * @param $pid
     * @return array
     */
    function recursive($data, $pid = 0)
    {
        $re_data = [];
        foreach ($data as $d) {
            if ($d->parent_id == $pid) {
                $re_data[$d->id] = $d;
                $re_data[$d->id]['children'] = recursive($data, $d->id);
            } else {
                continue;
            }
        }
        return array_values($re_data);
    }
}

if (!function_exists('push_message')) {
    /**
     * @param $scene
     * @param $data
     * @param array $accept_user_ids
     * @param int $send_user_id
     * @return bool
     * @throws GuzzleException
     */
    function push_message($scene,$data, $accept_user_ids = [], $send_user_id = 0)
    {
        try {
            $users = \App\Models\User::query()->pluck('nickname', 'id')->toArray();
            foreach ($accept_user_ids as $accept_user_id) {
                \App\Models\Message::create([
                    'send_user_id' => $send_user_id,
                    'send_user_nickname' => \Illuminate\Support\Arr::get($users, $send_user_id, null),
                    'accept_user_id' => $accept_user_id,
                    'accept_user_nickname' => \Illuminate\Support\Arr::get($users, $accept_user_id, null),
                    'title' => $data['title'] ?? null,
                    'content' => $data['content'] ?? null,
                ]);
            }
            $client = new \GuzzleHttp\Client();
            $client->post('http://127.0.0.1:9502', [
                'json' => [
                    'scene' => $scene,
                    'data' => $data['title']??null,
                    'user_ids' => $accept_user_ids,
                ],
                'timeout' => 5,
            ]);
            return true;
        } catch (Exception $exception) {
            \Illuminate\Support\Facades\Log::error('推送消息异常：' . $exception->getMessage());
        }
        return false;
    }
}



