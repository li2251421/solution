<?php

require_once '../vendor/autoload.php';

use App\ratelimiter\LeakyBucket;

$burst = 10;
$rate = 2;

for ($i = 1; $i <= 20; $i++) {
    usleep(100 * 1000);
    $res = LeakyBucket::reqLimit('test', $burst, $rate);
    dd($res ? 'ok' : 'refuse', "{$burst}容量的桶，每秒处理{$rate}个-第{$i}次");
}