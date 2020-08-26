<?php

require_once '../../vendor/autoload.php';

use App\io\asynchronous\Worker;

class Server
{
    public $server;

    public function __construct($host, $port, $type = 'tcp')
    {
        $this->server = new Worker($host, $port, $type);

        $this->server->on('connect', [$this, "onConnect"]);
        $this->server->on('receive', [$this, "onReceive"]);
        $this->server->on('close', [$this, "onClose"]);

        $this->server->start();
    }

    public function onConnect($socket, $fd)
    {
        dd('server: ' . $socket . ', fd: ' . $fd, 'onConnect');
        $str = 'The local time is ' . date('Y:m:d H:i:s');
        dd($str, "Connected后发送消息");
        $this->server->send($fd, $str);
    }

    public function onReceive($socket, $fd, $data)
    {
        dd('server: ' . $socket . ', fd: ' . $fd, 'onReceive');
        $str = "我收到了你的消息: " . $data;
        dd($str, "Received后回复");
        $this->server->send($fd, $str);
    }

    public function onClose($socket, $fd)
    {
        dd('server: ' . $socket . ', fd: ' . $fd, 'onClose');
    }
}

new Server('0.0.0.0', '8888', 'tcp');






