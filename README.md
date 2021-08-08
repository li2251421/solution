# 通用解决方案

## 网络IO模型(PHP)
- [event/libevent](https://github.com/li2251421/solution/tree/master/php/io/event)
高性能网络事件库
- [blocking IO](https://github.com/li2251421/solution/tree/master/php/io/blocking)
阻塞IO
- [non-blocking IO](https://github.com/li2251421/solution/tree/master/php/io/non-blocking)
非阻塞IO
- [multiplexing IO](https://github.com/li2251421/solution/tree/master/php/io/multiplexing) 
多路复用IO
- [signal-driven IO](https://github.com/li2251421/solution/tree/master/php/io/signal-driven)
信号驱动式IO
- [asynchronous IO](https://github.com/li2251421/solution/tree/master/php/io/asynchronous)
异步IO

## tcp(Go)
- [unpack](https://github.com/li2251421/solution/tree/master/go/tcp/unpack) 
msg_header+content_len+content解决粘包/拆包问题

## zookeeper(Go)
- [lock](https://github.com/li2251421/solution/blob/master/go/zookeeper/lock.go) 
分布式锁

## load_balance-负载均衡算法(Go)
- [random]() 
随机
- [round_robin]() 
轮询
- [weight_round_robin]() 
加权轮询
- [consistent_hash]()
一致性哈希

## 限流(PHP)
- [Counter](https://github.com/li2251421/solution/blob/master/php/ratelimiter/Counter.php)
计数器算法
- [SlidingWindowCounter](https://github.com/li2251421/solution/blob/master/php/ratelimiter/SlidingWindowCounter.php) 
滑动窗口计数器算法
- [LeakyBucket](https://github.com/li2251421/solution/blob/master/php/ratelimiter/LeakyBucket.php) 
漏桶算法
- [TokenBucket](https://github.com/li2251421/solution/blob/master/php/ratelimiter/TokenBucket.php) 
令牌桶算法

## Redis(PHP)
- [RedisPool](https://github.com/li2251421/solution/blob/master/php/redis/RedisPool.php) 
Redis连接池
- [RedisLock](https://github.com/li2251421/solution/blob/master/php/redis/RedisLock.php) 
Redis分布式锁
- [RedisClient](https://github.com/li2251421/solution/blob/master/php/redis/RedisClient.php) 
Redis客户端，支持单点、主从、哨兵、集群模式，主从延迟及哨兵模式下的故障切换

## 长链转短链(PHP)
- [ShortUrl](https://github.com/li2251421/solution/blob/master/php/shorturl/ShortUrl.php) 
前置发号器+redis+62进制表示法

## 分布式唯一ID(PHP/Go)
### 雪花算法
- [SnowflakeOnRedis-PHP](https://github.com/li2251421/solution/blob/master/php/uniqueid/SnowflakeOnRedis.php) 
- [SnowflakeOnSwoole-PHP](https://github.com/li2251421/solution/blob/master/php/uniqueid/SnowflakeOnSwoole.php) 
- [Snowflake-GO](https://github.com/li2251421/solution/blob/master/go/uniqueid/snowflake.go)

## 数据结构与算法(Go)
- [array](https://github.com/li2251421/solution/tree/master/go/algo/array) 
数组
- [string](https://github.com/li2251421/solution/tree/master/go/algo/string) 
字符串
- [linkedlist](https://github.com/li2251421/solution/tree/master/go/algo/linkedlist) 
链表
- [stack](https://github.com/li2251421/solution/tree/master/go/algo/stack) 
栈
- [queue](https://github.com/li2251421/solution/tree/master/go/algo/queue) 
队列
- [heap](https://github.com/li2251421/solution/tree/master/go/algo/heap) 
堆
- [sort](https://github.com/li2251421/solution/tree/master/go/algo/sort) 
排序
- [dp](https://github.com/li2251421/solution/tree/master/go/algo/dp) 
动态规划
- [double_pointer](https://github.com/li2251421/solution/tree/master/go/algo/double_pointer) 
双指针
- [sliding_window](https://github.com/li2251421/solution/tree/master/go/algo/sliding_window) 
滑动窗口
- [backtrack](https://github.com/li2251421/solution/tree/master/go/algo/traceback) 
回溯算法
- [math](https://github.com/li2251421/solution/tree/master/go/algo/math) 
数学问题
- [design](https://github.com/li2251421/solution/tree/master/go/algo/design) 
设计
  
## 常用库(Go)
- [wire](https://github.com/li2251421/solution/tree/master/go/library/wire)