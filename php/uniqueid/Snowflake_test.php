<?php

require_once '../vendor/autoload.php';

use App\uniqueid\{SnowflakeOnSwoole, SnowflakeOnRedis};
use \Swoole\Coroutine\Channel as chan;

//testOnSwoole();
testOnRedis();

function testOnSwoole()
{
    $workerId = 1;
    // 常驻内存方式，实例化一次
    $snowflake = new SnowflakeOnSwoole($workerId);

    $n = 10;
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
    $n = 10;

    $arr = [];
    $snowflake = SnowflakeOnRedis::getInstance($workerId);
    for ($i = 0; $i < $n; $i++) {
        $id = $snowflake->getId();
        if (in_array($id, $arr)) {
            exit("ID 已存在！重复ID：" . $id . "\n");
        }
        array_push($arr, $id);
        echo $id . "\n";
    }
    die;

    $snowflake = SnowflakeOnRedis::getInstance($workerId);
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


