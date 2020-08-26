<?php

require_once '../vendor/autoload.php';

use App\ratelimiter\SlidingWindowCounter;

for ($i = 1; $i <= 20; $i++) {
    $res = SlidingWindowCounter::reqLimit('reqlimit:SlidingWindowCounter', 10, 5);
    dd($res ? 'ok' : 'refuse', "10秒内限流5次-第{$i}次");
}