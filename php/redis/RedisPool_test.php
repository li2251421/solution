<?php

namespace Redis;

require_once '../vendor/autoload.php';

test();

function test()
{
    for ($i = 0; $i < 100; $i++) {
        go(function () use ($i) {
            $pool = RedisPool::getInstance();
            $redis = $pool->getConn();

            if ($redis) {
                $redis->set("test:" . $i, $i);
                echo "连接ok\n";
                $pool->freeConn($redis);
            } else {
                echo "获取连接失败";
            }
        });
    }
}




