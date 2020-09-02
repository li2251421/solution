<?php

require_once '../vendor/autoload.php';

use App\ratelimiter\TokenBucket;

$max = 2;
$ratelimter = new TokenBucket('test', $max);

$ratelimter->reset();
dd($max, '初始化令牌');

// 定时加入令牌
$ms = 1000;
swoole_timer_tick($ms, function () use ($ratelimter, $ms) {
    $num = 1;
    $ratelimter->add($num);
    dd($num, "每{$ms}ms加入令牌");
});

// 定时消耗令牌
$ms = 500;
swoole_timer_tick($ms, function () use ($ratelimter, $ms) {
    $res = $ratelimter->get();
    dd($res ? 'ok' : 'refuse', "每{$ms}ms获取令牌");
});