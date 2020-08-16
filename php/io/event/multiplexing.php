<?php

require_once '../../vendor/autoload.php';

$method = Event::getSupportedMethods();
dd($method, '当前平台支持的IO多路复用');

$eventBase = new EventBase();
dd($eventBase->getMethod(), '当前Event使用的IO多路复用');

$eventConfig = new EventConfig();
$eventConfig->avoidMethod('kqueue'); // 避免使用kqueue

dd('避免使用kqueue');

$eventBase = new EventBase($eventConfig);
dd($eventBase->getMethod(), '当前Event使用的IO多路复用');

$features = $eventBase->getFeatures();
if ($features & EventConfig::FEATURE_ET) {
    dd('边缘触发', '特性');
}
if ($features & EventConfig::FEATURE_O1) {
    dd('O1添加删除事件', '特性');
}
if ($features & EventConfig::FEATURE_FDS) {
    dd('任意文件描述符(不只socket)', '特性');
}
