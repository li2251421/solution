<?php

namespace App\ratelimiter;

/**
 * 计数器算法
 * 弊端：突刺现象（一定时间内的一小段时间就用完了所有资源，后大部分时间中无资源可用）
 */
class Counter
{
    public static function reqLimit($key, $expire, $limit)
    {
        $redis = self::getRedis();

        // lua脚本保证原子性
        $script = '
            local count = redis.call("GET", KEYS[1])
            if count then
                if count >= ARGV[2] then
                    return 0
                else 
                    redis.call("INCR", KEYS[1])
                    return 1
                end
            else
                redis.call("SET", KEYS[1], 1, "NX", "EX", ARGV[1])
                return 1
            end
        ';
        $res = $redis->eval($script, [$key, $expire, $limit], 1);
        return $res;
    }

    private static function getRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');

        return $redis;
    }
}