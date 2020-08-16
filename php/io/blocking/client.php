<?php

require_once '../../vendor/autoload.php';

$host = '0.0.0.0:8888';

$fd = stream_socket_client($host, $errno, $errstr);

//stream_set_blocking($fd, 0);

if (!$fd) {
    die("{$errstr} ({$errno}): " . $host);
}

dd($host . ' ' . $fd, Date('H:i:s') . ' 连接socket');

dd('', Date('H:i:s') . ' 睡眠2秒钟...');
// 延迟2s
sleep(2);


$str = "hello i'm client";
fwrite($fd, $str);
dd($str, Date('H:i:s') . ' 发送消息');

fclose($fd);
dd($fd, Date('H:i:s') . ' 关闭连接');