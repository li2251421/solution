# 通用解决方案-PHP

## 项目运行
### 环境依赖，依赖通用部署方案
项目地址： https://github.com/li2251421/deploy

docker exec -it php_base /bin/bash

cd /srv/www/vhosts/blog/php

composer install/composer dump-autoload

## 项目实现
### io-网络IO模型
- event/libevent 高性能网络事件库
- blocking IO 阻塞IO
- non-blocking IO 非阻塞IO
- multiplexing IO 多路复用IO
- signal-driven IO 信号驱动式IO
- asynchronous IO 异步IO

### ratelimiter-限流
- Counter 计数器算法
- SlidingWindowCounter 滑动窗口计数器算法
- LeakyBucket 漏桶算法
- TokenBucket 令牌桶算法

### redis
- RedisPool Redis连接池
- RedisLock Redis分布式锁
- RedisClient Redis客户端，支持单点、主从、哨兵、集群模式，主从延迟及哨兵模式下的故障切换

### shorturl-长链转短链
- ShortUrl 前置发号器+redis+62进制表示法

### uniqueid-分布式唯一ID
- SnowflakeOnSwoole 基于Swoole实现的Snowflake雪花算法
- SnowflakeOnRedis 基于Redis实现的Snowflake雪花算法 

### util-工具包
- Inotify 监控文件变化
- helper 公共方法

