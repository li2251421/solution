<?php

require_once '../../vendor/autoload.php';

$host = "tcp://0.0.0.0:8888";
$socket = stream_socket_server($host);
dd($host . ' ' . $socket, '创建socket');
// 设置非阻塞
stream_set_blocking($socket, 0);

$events = [];
$conns = [];

$eventBase = new EventBase();

$event = new Event($eventBase, $socket, Event::READ | Event::WRITE | Event::PERSIST, function ($socket) {
    global $events, $conns, $eventBase;
    if (false != $conn = stream_socket_accept($socket)) {
        dd($conn, '建立连接');
        stream_set_blocking($conn, 0);
        // 连接保存到数组
        $conns[(int)$conn] = $conn;

        $event = new Event($eventBase, $conn, Event::READ | Event::WRITE | Event::PERSIST, function ($conn) {
            global $conns;
            $buffer = fread($conn, 65535);
            if ($buffer) {
                dd($buffer, '收到' . $conn . '消息');

                // 消息广播
                dd($conns, '消息广播');
                foreach ($conns as $c) {
                    // if ($conn != $c) {
                    $msg = $conn . '说：' . $buffer;
                    fwrite($c, $msg);
                    // }
                }
            }
        });
        $event->add();
        // 需要保存事件，否则无法保持连接
        $events[(int)$conn] = $event;
    }
});

$event->add();
$eventBase->loop();