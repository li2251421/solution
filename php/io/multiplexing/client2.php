<?php

require_once '../../vendor/autoload.php';

$host = 'tcp://0.0.0.0:8888';

$fd = stream_socket_client($host, $errno, $errstr);

if (!$fd) {
    die("{$errstr} ({$errno}): " . $host);
}

dd($host . ' ' . $fd, Date('H:i:s') . ' 连接socket');

foreach (range(1, 2) as $i) {
    dd('', Date('H:i:s') . ' 睡眠2秒钟...');
    sleep(2);

    $str = "I'm client2, seq: {$i}";
    dd($str, Date('H:i:s') . ' 发送消息');
    fwrite($fd, $str);
}

fclose($fd);
dd($fd, Date('H:i:s') . ' 关闭连接');