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



