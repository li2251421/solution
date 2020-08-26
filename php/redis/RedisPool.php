<?php

namespace App\redis;

use Swoole\Coroutine\Channel;

/**
 * Class RedisPool
 * Redis连接池，基于Swoole
 */
class RedisPool
{
    protected $host; // redis host
    protected $prot; // redis port

    protected $maxConn = 30; // 最大连接数
    protected $minConn = 5; // 最小连接数，初始化时创建
    protected $chan; // 保存当前连接
    protected $timeout = 3; // 取连接的超时时间
    protected $count; // 当前连接个数
    protected $idelTime = 10; // 允许空闲时间，超过则进行回收 单位：秒
    protected $sleepTime = 200; // 拿不到连接的睡眠时间 单位：毫秒

    protected static $instance = null;

    private function __construct()
    {
        $this->host = "127.0.0.1";
        $this->port = "6379";
        $this->initConn();
        $this->gc();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取连接
     * @param int $blockTime 等待时间，单位：毫秒
     * @return mixed|null
     */
    public function getConn($blockTime = 0)
    {
        if ($this->chan->isEmpty()) {
            while ($blockTime >= 0) {
                if ($this->count < $this->maxConn) {
                    $conn = $this->createConn();
                    if ($conn) {
                        $this->chan->push($conn);
                        $this->count++;
                        break;
                    }
                }
                // 等待释放连接
                $blockTime -= $this->sleepTime;
                usleep($this->sleepTime * 1000);
            }
        }
        $conn = $this->chan->pop($this->timeout);
        return $conn ? $conn['redis'] : null;
    }

    // 释放连接
    public function freeConn(\Redis $redis)
    {
        $conn = [
            'last_used_time' => time(),
            'redis' => $redis
        ];
        $this->chan->push($conn);
        $this->count++;
    }

    // 初始化连接 minConn个连接
    protected function initConn()
    {
        $this->chan = new Channel($this->maxConn);
        for ($i = 0; $i < $this->minConn; $i++) {
            $conn = $this->createConn();
            if ($conn) {
                $this->chan->push($conn);
                $this->count++;
            }
        }
    }

    /**
     * 创建连接，返回数组 ['last_used_time' =>  , 'conn' =>  ]
     * @return array|bool
     */
    protected function createConn()
    {
        try {
            $redis = new \Redis();
            $redis->connect($this->host, $this->port);
            return [
                'last_used_time' => time(), // 记录上次连接时间，用于回收
                'redis' => $redis
            ];
        } catch (\Exception $e) {
            return false;
        }
    }

    // 回收空闲的连接
    protected function gc()
    {
        // 定时2秒检测
        swoole_timer_tick(2000, function () {
            $conns = [];
            while (true) {
                // 当前连接为空 或者 连接个数 <= 最小连接，不回收
                if ($this->chan->isEmpty() || $this->count <= $this->minConn) {
                    break;
                }
                $conn = $this->chan->pop();
                if (empty($conn)) {
                    continue;
                }
                // 若空闲时间大于idelTime，则进行回收
                if (time() - $conn['last_used_time'] > $this->idelTime) {
                    $this->count--;
                    $conn['redis'] = null;
                } else {
                    // 未超过空闲时间的连接放回去
                    array_push($conns, $conn);
                }
            }
            // 将连接放回通道
            foreach ($conns as $conn) {
                $this->chan->push($conn);
            }
        });
    }

}
