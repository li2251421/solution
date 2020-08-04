<?php

namespace Redis;

/**
 * Class RedisLock
 * 并发锁
 * set nx ex 代替 setnx + expire，防止setnx出问题lock永远不会被释放
 * lua脚本 先get后del 代替 直接del，防止删除自己处理锁过期后别人加的锁
 */
class RedisLock
{
    const LOCK_PREFIX = "redislock:";
    const DEFAULT_SLEEP_TIME = 0.05; // 毫秒，拿不到锁后的睡眠时间，防止一直消耗CPU

    public $redis;

    public function __construct()
    {
        // redis扩展
        $this->redis = new \Redis();
        echo "Connecting....\n";
        $this->redis->connect('127.0.0.1', 6379);
        echo "Connection to server successfully\n";
        echo "Server is running: " . $this->redis->ping() . "\n";
    }

    /**
     * @param $key
     * @param int $blockTime 等待时间，单位：毫秒
     * @param int $px 过期时间，单位：毫秒
     * @return $token or false
     */
    public function lock($key, $blockTime = 0, $px = 1000)
    {
        $token = uniqid(); // 生成一个随机token，解锁时通过token标识，防止解除别人加的锁
        while ($blockTime >= 0) {
            $res = $this->redis->set(self::LOCK_PREFIX . $key, $token, ["NX", "PX" => $px]);
            if ($res) {
                return $token;
            }
            $blockTime -= self::DEFAULT_SLEEP_TIME;
            usleep(self::DEFAULT_SLEEP_TIME * 1000); // 睡眠一会，防止cpu空转
        }
        return false;
    }

    public function unlock($key, $token)
    {
        // lua脚本保证原子性，判断锁的值等于$token才解锁(删除key)
        $script = '
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else 
                return 0
            end
        ';
        return $this->redis->eval($script, [self::LOCK_PREFIX . $key, $token], 1);
    }
}