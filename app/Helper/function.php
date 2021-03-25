<?php


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
     * 推送websocket消息
     * @param $data
     * @param array $user_ids
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    function push_message($data, $user_ids = [])
    {
        try {
            $client = new \GuzzleHttp\Client();
            $client->post('http://127.0.0.1:9502',[
                'form_params' => [
                    'data' => $data,
                    'user_ids' => $user_ids,
                ],
                'timeout' => 5,
            ]);
        }catch (Exception $exception){
            \Illuminate\Support\Facades\Log::error('推送消息异常：'.$exception->getMessage());
        }
        return;
    }
}



