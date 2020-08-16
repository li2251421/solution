<?php

/**
 * 阻塞IO，stream_socket_accept fread 阻塞调用
 */

require_once '../../vendor/autoload.php';

$host = '0.0.0.0:8888';

$socket = stream_socket_server($host, $errno, $errstr);

if (!$socket) {
    die("{$errstr} ({$errno}): " . $host);
}

dd($host . ' ' . $socket, Date('H:i:s') . ' 创建socket');

$conn = stream_socket_accept($socket);

dd('', Date('H:i:s') . ' 准备读取消息....');
// 堵塞调用
$msg = fread($conn, 1024);

dd($msg, Date('H:i:s') . ' 读取消息');

fclose($conn);
dd($conn, Date('H:i:s') . ' 关闭连接');

fclose($socket);
dd($socket, Date('H:i:s') . ' 关闭socket');