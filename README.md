# 通用解决方案

## 网络IO模型
- event/libevent 高性能网络事件库
- blocking IO 阻塞IO
- non-blocking IO 非阻塞IO
- multiplexing IO 多路复用IO
- signal-driven IO 信号驱动式IO
- asynchronous IO 异步IO

## 限流
- Counter 计数器算法
- SlidingWindowCounter 滑动窗口计数器算法
- LeakyBucket 漏桶算法
- TokenBucket 令牌桶算法

## Redis
- RedisPool Redis连接池
- RedisLock Redis分布式锁
- RedisClient Redis客户端，支持单点、主从、哨兵、集群模式，主从延迟及哨兵模式下的故障切换

## 长链转短链
- ShortUrl 前置发号器+redis+62进制表示法

## 分布式唯一ID
- Snowflake 雪花算法
