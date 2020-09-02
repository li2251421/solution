<?php

namespace App\ratelimiter;

/**
 * 漏桶算法，基于毫秒实现，redis hash维护上次处理时间和缓冲队列
 * 弊端：无法应对短时间的突发流量
 */
class LeakyBucket
{

    /**
     * 缓冲队列的单位缩小1000倍，转换成毫秒处理
     * @param $key
     * @param $burst 桶容量
     * @param $rate 处理速率 单位 r/s
     * @return bool
     */
    public static function reqLimit($key, $burst, $rate)
    {
        $key = self::getPrefix() . $key;
        $redis = self::getRedis();
        // lua脚本保证原子性
        $script = '
            if redis.call("EXISTS", KEYS[1]) == 1 then
                return redis.call("HMGET", KEYS[1], ARGV[1], ARGV[2])
            else
                return nil
            end
        ';
        $data = $redis->eval($script, [$key, 'last_time', 'burst_wait'], 1);
        $lastTime = $data[0] ?? self::getMilliSecond();
        $burstWait = $data[1] ?? 0;
        // 获取距离上次时间已经处理的数量
        $now = self::getMilliSecond();
        $burstHandled = floor(($now - $lastTime) * $rate);
        // 剩余待处理数量
        $burstWait = max(0, $burstWait - $burstHandled);
        dd("lastTime: {$lastTime},now:{$now},burstHandled:{$burstHandled},burstWait:{$burstWait}", 'debug');
        $result = false;
        if ($burstWait < $burst) {
            $burstWait += 1000; // 单位缩小1000倍，1个请求转换成1000
            $result = true;
        }
        $redis->hMSet($key, ['last_time' => $now, 'burst_wait' => $burstWait]);
        return $result;
    }

    private static function getRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');

        return $redis;
    }

    private static function getPrefix()
    {
        return "ratelimiter:leaky_bucket:";
    }

    private static function getMilliSecond()
    {
        return floor(microtime(true) * 1000);
    }
}