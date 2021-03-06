<?php

namespace App\uniqueid;

use App\redis\RedisLock;

/**
 * Class Snowflake
 * 64bit = 1bit(符号位固定0) + 41bit(毫秒时间戳) + 10bit(机器节点) + 12bit(序列号)
 * 基于redis非常驻内存，需要保存上次ID生成时间戳和时间戳对应序号，可通过redis保存，并对getId加锁
 * 时钟回拨问题，借用上次时间戳并报警通知
 */
class SnowflakeOnRedis
{
    const EPOTH = 1596284460 * 1000; // 起始时间戳，毫秒

    const SEQUENCE_BITS = 12; // 序号部分，12bit
    const SEQUENCE_MAX = (1 << self::SEQUENCE_BITS) - 1; // 序号最大值

    const WORKER_BITS = 10; // 机器节点，10bit
    const WORKER_MAX = (1 << self::WORKER_BITS) - 1; // 机器节点最大值

    const TIME_OFFSET = self::WORKER_BITS + self::SEQUENCE_BITS; // 时间戳部分左偏移量
    const WORKER_OFFSET = self::SEQUENCE_BITS; // 节点部分左偏移量

    protected $workerId; // 节点ID
    protected $lock; // 互斥锁，redis实现

    public function __construct($workerId)
    {
        if ($workerId < 0 || $workerId > self::WORKER_MAX) {
            throw new \Exception("Worker Id 超出范围");
            exit(0);
        }
        $this->lock = new RedisLock();
        $this->workerId = $workerId;
    }

    public function getId()
    {
        $lockKey = $this->millisecond();
        $lockToken = $this->lock->lock($lockKey, 0.1);
        if (!$lockToken) {
            return false;
        }
        $now = $this->millisecond();
        $lastTime = $this->lock->redis->get('snowflake:lasttime') ?: 0; // 非常驻内存，通过redis获取
        // 时钟回拨问题，借用上次时间戳并报警通知
        if ($lastTime > $now) {
            dd("workerId: {$this->workerId}, now: {$now}, lastTime: {$lastTime}", 'clock is turn back');
            $now = $lastTime;
        }
        $sequence = $this->lock->redis->get('snowflake:lasttime:' . $lastTime . ':sequence') ?: 0; // 非常驻内存，通过redis获取
        if ($lastTime == $now) {
            // 同一毫秒内，序号++
            $sequence++;
            if ($sequence > self::SEQUENCE_MAX) {
                // 当前毫秒序号用完，等待一下毫秒生成
                while ($now <= $lastTime) {
                    $now = $this->millisecond();
                }
            }
        } else {
            // 新的毫秒复位序号
            $sequence = 0;
        }
        $this->lock->redis->set("snowflake:lasttime", $now); // 记录上次时间戳
        $this->lock->redis->set('snowflake:lasttime:' . $now . ':sequence', $sequence, 1); // 记录上次sequence

        $id = ($now - self::EPOTH) << self::TIME_OFFSET | $this->workerId << self::WORKER_OFFSET | $sequence;

        $this->lock->unlock($lockKey, $lockToken);
        echo "lastTime: " . $now . ", sequence: " . $sequence . "\n";

        return $id;
    }

    private function millisecond()
    {
        return (int)(microtime(true) * 1000);
    }
}