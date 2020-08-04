# 通用解决方案-PHP

## 项目运行
### 基础环境依赖，可根据docker部署
项目地址： https://github.com/li2251421/dockers.git
### 某些实现依赖composer自动加载
- docker pull composer
- docker run --rm --interactive --tty --volume /project/solution/php:/app composer dump-autoload

## 项目实现
### redis
- RedisPool Redis连接池
- RedisLock Redis分布式锁

### uniqueid 分布式唯一ID
- SnowflakeOnSwoole 基于Swoole实现的Snowflake雪花算法
- SnowflakeOnRedis 基于Redis实现的Snowflake雪花算法 
未完待续...

