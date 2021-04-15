<?php

//namespace anerg\helper;

/**
 * Twitter的Snowflake生成全局唯一ID的PHP实现
 *
 * 可实现分布式全局唯一ID的生成
 *
 * 因为PHP不是常驻内存运行，所以无法实现原版的队列编号，采用随机数代替
 * 可以最大程度避免并发请求时生成相同的ID
 * 理论上，当MAX_RANDOM_BIT为12，也就是随机数最大值为4095时
 * 每毫秒需生成的id小于1000即可以安全的生成不冲突的id
 *
 * @author Coeus <r.anerg@gmail.com>
 */
class Snowflake
{
    //开始时间戳，固定小于当前时间的毫秒，用于缩减时间位数
    const EPOCH = 1575388800000;
    //最大机器标识位数，8->255 10->1023
    const MAX_MACHINE_BIT = 8;
    //最大随机数位数，12->4095
    const MAX_RANDOM_BIT = 12;

    /**
     * 获取一个10进制唯一ID
     *
     * 如果$machineId为0，则会忽略掉机器标识位
     *
     * @param integer   $machineId
     * @param array     $options    数组形式，只包含epoch，maxMachineBit和maxRandomBit
     * @return integer
     */
    public static function nextId($machineId = 1, $options = [])
    {
        $epoch         = isset($options['epoch']) ? $options['epoch'] : self::EPOCH;
        $maxMachineBit = isset($options['maxMachineBit']) ? $options['maxMachineBit'] : self::MAX_MACHINE_BIT;
        $maxRandomBit  = isset($options['maxRandomBit']) ? $options['maxRandomBit'] : self::MAX_RANDOM_BIT;
        //机器标识范围判断
        $maxMachineId = ~(-1 << $maxMachineBit);
        if ($machineId > $maxMachineId || $machineId < 0) {
            throw new \Exception("MachineId can't be less than " . $maxMachineId . " or greater than 0");
        }
        //毫秒时间戳
        $time = \floor(\microtime(true) * 1000);
        $time -= $epoch;

        //生成随机数，因为php不是内存持久化的，在并发时无法做到维护一个唯一的序列，所以用随机数替代
        $random = \mt_rand(0, ~(-1 << $maxRandomBit));

        //组合数据 {时间戳差值|机器标识|随机数}
        //机器标识为0则抛弃掉机器标识
        if ($machineId == 0) {
            //时间戳要左移的位数
            $timeLeftShift = $maxRandomBit;
            $nextId        = ($time << $timeLeftShift) | $random;
        } else {
            //时间戳要左移的位数
            $timeLeftShift = $maxRandomBit + $maxMachineBit;
            $nextId        = ($time << $timeLeftShift) | ($machineId << $maxRandomBit) | $random;
        }

        return $nextId;
    }

    /**
     * 获取一个36进制的唯一ID
     *
     * @param integer $machineId
     * @return string
     */
    public static function nextHash($machineId = 0, $options = [])
    {
        return \strtoupper(\base_convert(self::nextId($machineId, $options), 10, 36));
    }
}
