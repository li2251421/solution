<?php

require_once '../../vendor/autoload.php';

// 初始化EventConfig
$eventConfig = new EventConfig();
// 初始化EventBase
$eventBase = new EventBase($eventConfig);


// 间隔,秒
$tick = 0.1;

/**
 * 创建一个定时器
 * EventBase $base
 * mixed $fd 文件描述符，socket、文件、stream、时钟时间(-1)、信号(SIGHUP)
 * int $what 事件类型, Event::READ、Event::WRITE、Event::SIGNAL、Event::TIMEOUT Event::PERSIST
 * callable $cb
 * [, mixed $arg = NULL] 自定义数据，传递给回调函数
 */

$timer = new Event($eventBase, -1, Event::TIMEOUT | Event::PERSIST, function ($fd, $what, $tick) {
    dd(microtime(true), $tick . 's定时执行');
}, $tick);
// 将event挂起
$timer->add($tick);
// 执行进入循环
dd('进入循环');
$eventBase->loop();