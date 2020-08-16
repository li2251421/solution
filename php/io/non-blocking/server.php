<?php

/**
 * 非阻塞IO,stream_set_blocking($conn, 0),fread fgets总是会立即返回
 */

require_once '../../vendor/autoload.php';

$host = '0.0.0.0:8888';

$socket = stream_socket_server($host, $errno, $errstr);


if (!$socket) {
    die("{$errstr} ({$errno}): " . $host);
}

dd($host . ' ' . $socket, Date('H:i:s') . ' 创建socket');

$conn = stream_socket_accept($socket);

// 设置为非堵塞模式
stream_set_blocking($conn, 0);

// 轮询读取
while (true) {
    dd('', Date('H:i:s') . ' 准备读取消息....');
    // 非堵塞调用，立即返回结果
    $msg = fread($conn, 1024);
    dd($msg, Date('H:i:s') . ' 读取消息');
    if ($msg) {
        break;
    }
    sleep(1);
}
fclose($conn);
dd($conn, Date('H:i:s') . ' 关闭连接');

fclose($socket);
dd($socket, Date('H:i:s') . ' 关闭socket');