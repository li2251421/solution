# 通用解决方案-PHP

## 项目运行
### 环境依赖，参考通用部署方案
项目地址： https://github.com/li2251421/deploy
### 依赖composer自动加载
- docker pull composer
- docker run --rm --interactive --tty --volume /project/solution/php:/app composer dump-autoload

## 项目实现
### redis
- RedisPool Redis连接池
- RedisLock Redis分布式锁
- RedisClient Redis客户端，支持单点、主从、哨兵、集群模式，主从延迟及哨兵模式下的故障切换

### uniqueid 分布式唯一ID
- SnowflakeOnSwoole 基于Swoole实现的Snowflake雪花算法
- SnowflakeOnRedis 基于Redis实现的Snowflake雪花算法 
未完待续...

