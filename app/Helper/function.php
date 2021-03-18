<?php


if (!function_exists('uuid_generate')) {
    /**
     * 生成唯一不重复的uuid
     * @return string
     */
    function uuid_generate()
    {
        return md5(\Illuminate\Support\Facades\Redis::incr('uuid_generate_id') . uniqid(mt_rand(),true));
    }
}



