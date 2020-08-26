<?php

/**
 * 异步IO，基于Swoole\Event(epoll的封装)实现
 * linux的网络IO中是不存在异步IO的，linux的网络IO处理的第二阶段总是阻塞等待数据copy完成的。
 * epoll 因为采用 mmap的机制, 使得 内核socket buffer和 用户空间的 buffer共享, 从而省去了 socket data copy
 */

namespace App\io\asynchronous;

use Swoole\Event;

class Worker
{
    private $socket = null;

    // 闭包回调
    public $onConnect = null;
    public $onReceive = null;
    public $onClose = null;

    private $events = [
        'connect' => 'onConnect',
        'receive' => 'onReceive',
        'close' => 'onClose'
    ];

    public function __construct($host, $port, $type = 'tcp')
    {
        // 创建socket + bind + listen
        $local_socket = "{$type}://$host:{$port}";
        $this->socket = stream_socket_server($local_socket, $errno, $errstr);
        if (!$this->socket) {
            throw new \Exception("{$errstr} ({$errno})\n");
        }
        dd($local_socket, '创建socket: ' . $this->socket);
    }

    // 绑定事件
    public function on($e, $callback)
    {
        $event = $this->events[$e] ?? null;
        if ($event) {
            $this->$event = $callback;
        }
    }

    public function start()
    {
        $this->accept();
    }

    public function send($client, $data)
    {
        // 浏览器响应
        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Content-Type: text/html;charset=UTF-8\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Content-length: " . strlen($data) . "\r\n\r\n";
        $response .= $data;
        // 普通响应
        $response = $data;
        fwrite($client, $response);
    }

    protected function accept()
    {
        Event::add($this->socket, $this->acceptSocket());
    }

    // 接收连接
    protected function acceptSocket()
    {
        return function ($socket) {
            // 接收连接
            $conn = stream_socket_accept($socket);
            if (!$conn) {
                return;
            }

            if (is_callable($this->onConnect)) {
                ($this->onConnect)($socket, $conn);
            }

            Event::add($conn, $this->handleSocket());
        };
    }

    // 处理连接
    protected function handleSocket()
    {
        return function ($client) {
            $buffer = fread($client, 65535);
            if (empty($buffer) && (feof($client) || !is_resource($client))) {
                swoole_event_del($client);
                fclose($client);
                if (is_callable($this->onClose)) {
                    ($this->onClose)($this->socket, $client);
                }
                return;
            }
            if (!empty($buffer) && is_callable($this->onReceive)) {
                ($this->onReceive)($this->socket, $client, $buffer);
            }
        };
    }
}