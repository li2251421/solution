<?php

namespace Uniqueid;

use Swoole\Lock;

/**
 * Class Snowflake
 * 64bit = 1bit(符号位固定0) + 41bit(毫秒时间戳) + 10bit(机器节点) + 12bit(序列号)
 */
class SnowflakeOnSwoole
{
    const EPOTH = 1596284460 * 1000; // 起始时间戳

    const SEQUENCE_BITS = 12; // 序号部分，12bit
    const SEQUENCE_MAX = 1 << self::SEQUENCE_BITS - 1; // 序号最大值

    const WORKER_BITS = 10; // 机器节点，10bit
    const WORKER_MAX = 1 << self::WORKER_BITS - 1; // 机器节点最大值

    const TIME_OFFSET = self::WORKER_BITS + self::SEQUENCE_BITS; // 时间戳部分左偏移量
    const WORKER_OFFSET = self::SEQUENCE_BITS; // 节点部分左偏移量

    protected $timestamp; // 上次ID生成时间戳
    protected $workerId; // 节点ID
    protected $sequence; // 序号
    protected $lock; // Swoole互斥锁

    public function __construct($workerId)
    {
        if ($workerId < 0 || $workerId > self::WORKER_BITS) {
            throw new \Exception("Worker Id 超出范围");
            exit(0);
        }
        $this->timestamp = 0; // 常驻内存，初始化一次
        $this->workerId = $workerId;
        $this->sequence = 0; // 常驻内存，初始化一次
        $this->lock = new Lock(SWOOLE_MUTEX);
    }

    public function getId()
    {
        $this->lock->lock();
        $now = $this->millisecond();
        if ($this->timestamp == $now) {
            // 同一毫秒内，序号++
            $this->sequence++;
            if ($this->sequence > self::SEQUENCE_MAX) {
                // 当前毫秒序号用完，等待一下毫秒生成
                while ($now <= $this->timestamp) {
                    $now = $this->millisecond();
                }
            }
        } else {
            // 新的毫秒复位序号
            $this->sequence = 0;
        }
        $this->timestamp = $now; // 记录上次时间戳

        $id = ($now - self::EPOTH) << self::TIME_OFFSET | $this->workerId << self::WORKER_OFFSET | $this->sequence;

        $this->lock->unlock();
        echo "timestamp: " . $this->timestamp . ", sequence: " . $this->sequence . "\n";
        return $id;
    }

    private function millisecond()
    {
        return (int)(microtime(true) * 1000);
    }
}