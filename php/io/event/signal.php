<?php

require_once '../../vendor/autoload.php';

// 初始化EventConfig
$eventConfig = new EventConfig();
// 初始化EventBase
$eventBase = new EventBase($eventConfig);

/**
 * 创建一个定时器
 * EventBase $base
 * mixed $fd 文件描述符，socket、文件、stream、时钟时间(-1)、信号(SIGHUP)
 * int $what 事件类型, Event::READ、Event::WRITE、Event::SIGNAL、Event::TIMEOUT Event::PERSIST
 * callable $cb
 * [, mixed $arg = NULL] 自定义数据，传递给回调函数
 */

//$timer = new Event($eventBase, SIGUSR1, Event::SIGNAL, function () {
//    dd('SIGUSR1: ' . SIGUSR1, '接收到信号退出');
//});
$timer = new Event($eventBase, SIGUSR2, Event::SIGNAL | Event::PERSIST, function () {
    dd('SIGUSR1: ' . SIGUSR2, '接收到信号不退出');
});
// 将event挂起
$timer->add();
// 执行进入循环
dd('进入循环');
dd(posix_getpid(), '进程ID');
$eventBase->loop();