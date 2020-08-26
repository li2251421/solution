<?php

require_once '../vendor/autoload.php';

use App\util\Inotify;

$inotify = new Inotify(__DIR__, watchEvent());
$inotify->start();

function watchEvent()
{
    return function ($event) {
        switch ($event['mask']) {
            case IN_CREATE:
                break;
            case IN_DELETE:
                break;
            case IN_MODIFY:
                break;
            case IN_MOVE:
                break;
        }
        dd(Date('Y-m-d H:i:s') . ': ' . $event['mask'], $event['name']);
    };
}