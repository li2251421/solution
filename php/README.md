# 通用解决方案-PHP

## 项目运行
### 环境依赖，依赖通用部署方案
项目地址： https://github.com/li2251421/deploy

docker exec -it php_base /bin/bash

cd /srv/www/vhosts/blog/php

composer install/composer dump-autoload

## 项目实现
### io-网络IO模型
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

### ratelimiter-限流
- [Counter](https://github.com/li2251421/solution/blob/master/php/ratelimiter/Counter.php)
计数器算法
- [SlidingWindowCounter](https://github.com/li2251421/solution/blob/master/php/ratelimiter/SlidingWindowCounter.php) 
滑动窗口计数器算法
- [LeakyBucket](https://github.com/li2251421/solution/blob/master/php/ratelimiter/LeakyBucket.php) 
漏桶算法
- [TokenBucket](https://github.com/li2251421/solution/blob/master/php/ratelimiter/TokenBucket.php) 
令牌桶算法

### redis
- [RedisPool](https://github.com/li2251421/solution/blob/master/php/redis/RedisPool.php) 
Redis连接池
- [RedisLock](https://github.com/li2251421/solution/blob/master/php/redis/RedisLock.php) 
Redis分布式锁
- [RedisClient](https://github.com/li2251421/solution/blob/master/php/redis/RedisClient.php) 
Redis客户端，支持单点、主从、哨兵、集群模式，主从延迟及哨兵模式下的故障切换

### shorturl-长链转短链
- [ShortUrl](https://github.com/li2251421/solution/blob/master/php/shorturl/ShortUrl.php) 
前置发号器+redis+62进制表示法

### uniqueid-分布式唯一ID
- [SnowflakeOnRedis](https://github.com/li2251421/solution/blob/master/php/uniqueid/SnowflakeOnRedis.php) 
- [SnowflakeOnSwoole](https://github.com/li2251421/solution/blob/master/php/uniqueid/SnowflakeOnSwoole.php) 

### util-工具包
- [Inotify](https://github.com/li2251421/solution/blob/master/php/util/Inotify.php) 
监控文件变化
- [helper](https://github.com/li2251421/solution/blob/master/php/util/helper.php) 
公共方法

