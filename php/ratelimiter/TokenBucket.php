<?php

namespace App\ratelimiter;

/**
 * 令牌桶算法，基于redis list维护令牌数
 * 面对瞬间大流量，可以在短时间内请求拿到大量令牌
 */
class TokenBucket
{
    private $redis;
    private $bucketKey; // 令牌桶
    private $max; // 最大令牌

    public function __construct($key, $max)
    {
        $this->bucketKey = $this->getPrefix() . $key;
        $this->max = $max;
        $this->redis = $this->getRedis();
    }

    /**
     * 加入令牌
     * @param int $num 加入令牌的数量
     * @return int 加入成功的数量
     */
    public function add($num = 0)
    {
        // 当前令牌数
        $curnum = intval($this->redis->lLen($this->bucketKey));
        // 可加入令牌数
        $num = $curnum + $num > $this->max ? $this->max - $curnum : $curnum + $num;
        if ($num > 0) {
            $tokens = array_fill(0, $num, 1);
            $this->redis->lPush($this->bucketKey, ...$tokens);
            return $num;
        }
        return 0;
    }

    /**
     * 获取令牌
     * @return bool
     */
    public function get()
    {
        return $this->redis->rPop($this->bucketKey) ? true : false;
    }

    /**
     * 重置令牌到最大值
     */
    public function reset()
    {
        $this->redis->del($this->bucketKey);
        $this->add($this->max);
    }

    private function getRedis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');

        return $redis;
    }

    private function getPrefix()
    {
        return "ratelimiter:token_bucket:";
    }
}