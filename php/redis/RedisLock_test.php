<?php

require_once '../vendor/autoload.php';

use App\redis\RedisLock;

$redisLock = new RedisLock();

$key = "lock";
$token = $redisLock->lock($key, 1000, 2 * 1000);
echo "token: " . $token;
if ($token) {
    $redisLock->unlock($key, $token . '1');
} else {
    echo "加锁超时";
}