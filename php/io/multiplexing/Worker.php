<?php

/**
 * 多路复用IO，stream_select($read, $write, $except, 60) 选择可读可写连接
 */

namespace IO\multiplexing;

class Worker
{
    private $socket = null;
    private $conns = null;

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
        stream_set_blocking($this->socket, 0);
        $this->conns[(int)$this->socket] = $this->socket;
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
        while (true) {
            $read = $this->conns;
            $write = $except = [];
            stream_select($read, $write, $except, 60);

            foreach ($read as $client) {
                if ($client === $this->socket) {
                    // 当前连接
                    $this->acceptSocket();
                } else {
                    $this->handleSocket($client);
                }
            }
        }
        fclose($this->socket);
    }

    // 接收连接
    protected function acceptSocket()
    {
        // 接收连接
        $conn = stream_socket_accept($this->socket);
        if (!$conn) {
            return;
        }

        if (is_callable($this->onConnect)) {
            ($this->onConnect)($this->socket, $conn);
        }
        $this->conns[(int)$conn] = $conn;
    }

    // 处理连接
    protected function handleSocket($client)
    {
        $buffer = fread($client, 65535);
        if (empty($buffer) && (feof($client) || !is_resource($client))) {
            unset($this->conns[(int)$client]);
            fclose($client);
            if (is_callable($this->onClose)) {
                ($this->onClose)($this->socket, $client);
            }
            return;
        }
        if (!empty($buffer) && is_callable($this->onReceive)) {
            ($this->onReceive)($this->socket, $client, $buffer);
        }
    }
}