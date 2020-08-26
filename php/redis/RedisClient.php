<?php

namespace App\redis;

/**
 * Class RedisClient
 * Redis客户端，支持单点、主从、哨兵、集群模式，主从延迟及哨兵模式下的故障切换
 */
class RedisClient
{

    protected $config;
    protected $type; // 当前模式, normal-单点，默认 ms-主从 sentinel-哨兵 cluster-集群
    protected $method; // 设置当前读写模式，write, read
    protected $nodes; // 节点列表 ['master' => 'ip:port', 'slaves' => ['ip:port' => 'ip:port', ...], 'sentinels' => ['ip:port', ...], 'clusters' => ['ip:port', ...] ]
    protected $conns; // 连接列表 ['ip:port' => conn, 'ip:port' => conn, ....]
    protected $slotNodes; // 集群槽映射 ['0-1000' => 'ip:port', '1001-2000' => 'ip:port', ....]

    public function __construct($config)
    {
        $this->config = $config;
        $this->method = 'read'; // 默认读
        $this->type = $this->config['type'] ?? 'normal'; // 默认单点模式
        $this->{$this->type . 'Init'}(); // 初始化
        // 节点定时维护,主从延迟和哨兵故障转移
        $this->nodeMaintain();
    }

    // 设置当前读模式
    public function setRead()
    {
        $this->method = 'read';
        return $this;
    }

    // 设置当前写模式
    public function setWrite()
    {
        $this->method = 'write';
        return $this;
    }

    public function __call($cmd, $params)
    {
        $redis = $this->getRedis($cmd, $params);
        if (!$redis) {
            throw new \Exception("Redis 连接获取失败，cmd: {$cmd}");
        }
        try {
            return $redis->{$cmd}(...$params);
        } catch (\Exception $e) {
            throw new \Exception("Redis 方法执行失败，cmd: {$cmd}");
        }
    }

    // 获取redis连接
    protected function getRedis($cmd, $params)
    {
        switch ($this->type) {
            case 'normal':
                return $this->getConn('master');
                break;
            case 'ms':
            case 'sentinel':
                // if (in_array($cmd, ['set', 'setex', 'incr', 'incrby', 'decr', 'decrby'])) {
                if ($this->method == 'write') {
                    // 写命令 选择主库
                    return $this->getConn('master');
                } else {
                    // 读命令 选择从库
                    return $this->getConn('slaves');
                }
                break;
            case 'cluster':
                return $this->getClusterMaster($params[0]);
                break;
            default:
                throw new \Exception("不支持此模式：({$this->type})");
        }
    }

    // 单点模式初始化
    protected function normalInit()
    {
        $nodeKey = $this->getNodeKey($this->config['master']['host'], $this->config['master']['port']);
        $this->nodes['master'] = $nodeKey;
        if (empty($this->conns[$nodeKey])) {
            $this->conns[$this->nodes['master']] = $this->createConn($this->config['master']['host'], $this->config['master']['port']);
        }
    }

    // 主从模式初始化
    protected function msInit()
    {
        $nodeKey = $this->getNodeKey($this->config['master']['host'], $this->config['master']['port']);
        $this->nodes['master'] = $nodeKey;
        if (empty($this->conns[$nodeKey])) {
            $this->conns[$this->nodes['master']] = $this->createConn($this->config['master']['host'], $this->config['master']['port']);
        }
        $this->nodes['slaves'] = [];
        foreach ($this->config['slaves'] as $node) {
            // 需要用关联数组，主从延迟时需要删除从节点
            $nodeKey = $this->getNodeKey($node['host'], $node['port']);
            $this->nodes['slaves'][$nodeKey] = $nodeKey;
            if (empty($this->conns[$nodeKey])) {
                $this->conns[$nodeKey] = $this->createConn($node['host'], $node['port']);
            }
        }
    }

    // 哨兵模式初始化
    protected function sentinelInit()
    {
        $this->nodes['master'] = '';
        $this->nodes['slaves'] = [];
        $this->nodes['sentinel'] = [];
        foreach ($this->config['sentinel']['nodes'] as $node) {
            $nodeKey = $this->getNodeKey($node['host'], $node['port']);
            $this->nodes['sentinel'][] = $nodeKey;
            if (empty($this->conns[$nodeKey])) {
                $this->conns[$nodeKey] = $this->createConn($node['host'], $node['port']);
            }
        }
        $this->setSentinelMasterAndSlaves();
    }

    // 设置哨兵模式的主从节点
    protected function setSentinelMasterAndSlaves()
    {
        // 获取master slaves
        $conn = $this->getConn('sentinel');
        $masterInfo = $conn->rawCommand("sentinel", "get-master-addr-by-name", $this->config['sentinel']['master_name']);
        // 更新 主从config
        $this->config['master'] = [
            'host' => $masterInfo[0],
            'port' => $masterInfo[1]
        ];
        //dd($this->config['master'], "哨兵模式获取主节点");
        $slavesInfo = $conn->rawCommand("sentinel", "slaves", $this->config['sentinel']['master_name']);

        $this->config['slaves'] = [];
        foreach ($slavesInfo as $info) {
            if (!empty($info[7])) {
                // runid不为空，sentinel slaves会维护下线的节点
                $this->config['slaves'][] = [
                    'host' => $info[3],
                    'port' => $info[5]
                ];
            }
        }
        //dd($this->config['slaves'], "哨兵模式获取从节点");
        // 主从初始化
        $this->msInit();
    }

    // 集群模式初始化
    protected function clusterInit()
    {
        $this->nodes['cluster'] = [];
        foreach ($this->config['cluster']['nodes'] as $node) {
            $nodeKey = $this->getNodeKey($node['host'], $node['port']);
            $this->nodes['cluster'][] = $nodeKey;
            if (empty($this->conns[$nodeKey])) {
                $this->conns[$nodeKey] = $this->createConn($node['host'], $node['port']);
            }
        }
        // 初始化槽映射
        $conn = $this->getConn('cluster');
        $slotInfo = $conn->rawcommand("cluster", 'slots');
        foreach ($slotInfo as $info) {
            $slotKey = $this->slotKeyByRange($info[0], $info[1]);
            $this->slotNodes[$slotKey] = $this->getNodeKey($info[2][0], $info[2][1]);
        }
    }

    // 根据key所在槽找到对应master节点
    protected function getClusterMaster($key)
    {
        $slot = $this->getClusterSlot($key);
        dd($slot, "key:'{$key}' 对应slot");
        $conn = null;
        foreach ($this->slotNodes as $slotKey => $node) {
            list($start, $end) = $this->rangeBySlotKey($slotKey);
            if ($slot >= $start && $slot <= $end) {
                dd($node, "slot:'{$slot}' 对应master节点");
                $conn = $this->conns[$node] ?? null;
                break;
            }
        }
        return $conn;
    }

    // 根据key获取哈希槽
    protected function getClusterSlot($key)
    {
        // 根据{} 提取key
        if (false !== $start = strpos($key, '{')) {
            if (false !== ($end = strpos($key, '}', $start)) && $end !== ++$start) {
                $key = substr($key, $start, $end - $start);
            }
        }
        return crc16hash($key) % 16384;
    }

    // 创建连接
    protected function createConn($host, $port)
    {
        try {
            $redis = new \Redis();
            $redis->pconnect($host, $port);
            dd("{$host}:{$port} connect success", '创建连接');
            return $redis;
        } catch (\Exception $e) {
            dd($host . ":" . $port . " 连接异常", '创建连接');
            return null;
        }
    }

    /**
     * 获取一个连接，通过负载均衡算法
     * @param string $type master slaves sentinel cluster
     */
    protected function getConn($type = 'slaves')
    {
        if ($type == 'master') {
            return $this->conns[$this->nodes['master']] ?? null;
        }
        // 负载均衡-随机算法
        $randKey = array_rand($this->nodes[$type]);
        dd("{$type}：" . $this->nodes[$type][$randKey], '随机选择连接');
        return $this->conns[$this->nodes[$type][$randKey]] ?? null;
    }

    // 节点定时维护，主从延迟，哨兵故障转移
    protected function nodeMaintain()
    {
        // 只有主从和哨兵模式需要维护
        if (!in_array($this->type, ['ms', 'sentinel'])) {
            return;
        }
        swoole_timer_tick(2000, function () {
            if ($this->type == 'sentinel') {
                // 哨兵模式维护故障转移后的节点
                $this->setSentinelMasterAndSlaves();
            }
            $this->delay();
        });
    }

    // 主从延迟处理,主从偏移量超过1000，删除从节点
    protected function delay()
    {
        try {
            // 故障迁移后使用的是原主节点的连接
            $redis = $this->getConn('master');
            $replInfo = $redis->info('replication');
        } catch (\Exception $e) {
            dd('Redis 主从延迟处理失败', 'delay');
            return;
        }

        $masterOffset = $replInfo['master_repl_offset'];
        for ($i = 0; $i < $replInfo['connected_slaves']; $i++) {
            $slaveInfo = stringToArr($replInfo['slave' . $i]);
            // 主从偏移量超过1000，删除从节点
            $nodeKey = $this->getNodeKey($slaveInfo['ip'], $slaveInfo['port']);
            if ($masterOffset - $slaveInfo['offset'] >= 1000) {
                dd($nodeKey, '主从延迟删除节点');
                unset($this->nodes['slaves'][$nodeKey]);
                // unset($this->conns[$nodeKey]); 保留连接
            } else {
                // 恢复从节点
                $this->nodes['slaves'][$nodeKey] = $nodeKey;
                dd($nodeKey, '主从延迟恢复节点');
                if (empty($this->conns[$nodeKey])) {
                    $this->conns[$nodeKey] = $this->createConn($slaveInfo['ip'], $slaveInfo['port']);
                }
            }
        }
    }

    // 获取节点key ip:port
    protected function getNodeKey($ip, $port)
    {
        return $ip . ':' . $port;
    }

    // 根据槽范围获取槽节点key
    protected function slotKeyByRange($start, $end)
    {
        return $start . '-' . $end;
    }

    // 根据槽节点key获取范围
    protected function rangeBySlotKey($slotKey)
    {
        return explode('-', $slotKey);
    }

}