<?php

namespace Uniqueid;

require_once '../vendor/autoload.php';

use \Swoole\Coroutine\Channel as chan;

//testOnSwoole();
testOnRedis();

function testOnSwoole()
{
    $workerId = 1;
    // 常驻内存方式，实例化一次
    $snowflake = new SnowflakeOnSwoole($workerId);

    $n = 100;
    $chan = new chan($n);

    for ($i = 0; $i < $n; $i++) {
        go(function () use ($snowflake, $chan) {
            $id = $snowflake->getId();
            $chan->push($id);
        });
    }
    go(function () use ($n, $chan) {
        $arr = [];
        for ($i = 0; $i < $n; $i++) {
            $id = $chan->pop();
            if (in_array($id, $arr)) {
                exit("ID 已存在！重复ID：" . $id . "\n");
            }
            array_push($arr, $id);
            echo $id . "\n";
        }
    });
}


function testOnRedis()
{
    $workerId = 1;
    $n = 100;

    $arr = [];
    // 非常驻内存，每次都需实例化，这里为了测试实例化一次
//    $snowflake = new SnowflakeOnRedis($workerId);
//    for ($i = 0; $i < $n; $i++) {
//        // $snowflake = new SnowflakeOnRedis($workerId);
//        $id = $snowflake->getId();
//        if (in_array($id, $arr)) {
//            exit("ID 已存在！重复ID：" . $id . "\n");
//        }
//        array_push($arr, $id);
//        echo $id . "\n";
//    }

    $snowflake = new SnowflakeOnRedis($workerId);
    $chan = new chan($n);
    for ($i = 0; $i < $n; $i++) {
        go(function () use ($snowflake, $chan) {
            $id = $snowflake->getId();
            $chan->push($id);
        });
    }
    go(function () use ($n, $chan) {
        $arr = [];
        for ($i = 0; $i < $n; $i++) {
            $id = $chan->pop();
            if (in_array($id, $arr)) {
                exit("ID 已存在！重复ID：" . $id . "\n");
            }
            array_push($arr, $id);
            echo $id . "\n";
        }
    });
}


