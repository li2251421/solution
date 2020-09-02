<?php

namespace App\ratelimiter;

/**
 * 滑动窗口计数器算法，基于redis zset维护时间窗口
 * 窗口精确度为1ms，若并发较大，可设置score为微秒
 */
class SlidingWindowCounter
{
    /**
     * @param $key
     * @param $expire 过期时间，单位：秒
     * @param int $limit 限制，单位：次数
     * @return bool
     */
    public static function reqLimit($key, $expire, $limit)
    {
        $key = self::getPrefix() . $key;
        $redis = self::getRedis();
        $now = self::getMilliSecond();

        $pipe = $redis->multi(\Redis::PIPELINE); // 开启事务
        $pipe->zAdd($key, $now, $now . '-' . mt_rand()); // 分数设为当前毫秒，value加随机值(同一毫秒访问)
        $pipe->zRemRangeByScore($key, 0, $now - $expire * 1000); // 移动窗口(清除过期成员)
        $pipe->zCard($key);
        $pipe->expire($key, $expire);

        $result = $pipe->exec(); // 提交事务

        return $result[2] <= $limit;
    }

    private static function getRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');

        return $redis;
    }

    private static function getMilliSecond()
    {
        return (int)(microtime(true) * 1000);
    }

    private static function getPrefix()
    {
        return "retelimiter:sliding_window_counter:";
    }
}