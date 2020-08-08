<?php

namespace Redis;

require_once '../vendor/autoload.php';

$config = [
    // 类型, normal-单点(默认), ms-主从, sentinel-哨兵, cluster-集群
    'type' => 'normal',
    'master' => [
        'host' => '192.168.1.79',
        'port' => 6379,
    ],
    'slaves' => [
        [
            'host' => '192.168.1.80',
            'port' => 6379,
        ], [
            'host' => '192.168.1.81',
            'port' => 6379,
        ],
    ],
    'sentinel' => [
        'master_name' => 'mymaster', // 指定哨兵监控主节点的别名
        'nodes' => [ // 配置的是哨兵的节点
            [
                'host' => '192.168.1.179',
                'port' => 26379
            ], [
                'host' => '192.168.1.180',
                'port' => 26379
            ], [
                'host' => '192.168.1.181',
                'port' => 26379
            ]
        ]
    ],
    'cluster' => [
        'nodes' => [
            [
                'host' => '192.168.1.200',
                'port' => 6379,
            ], [
                'host' => '192.168.1.201',
                'port' => 6379,
            ], [
                'host' => '192.168.1.202',
                'port' => 6379,
            ], [
                'host' => '192.168.1.203',
                'port' => 6379,
            ], [
                'host' => '192.168.1.204',
                'port' => 6379,
            ], [
                'host' => '192.168.1.205',
                'port' => 6379,
            ],
        ]
    ]
];

// 单点模式
//$config['type'] = 'normal';
//$redisClient = new RedisClient($config);
//
//$res = $redisClient->set('normal', 11);
//dd($res, "set normal 11");
//$res = $redisClient->get('normal');
//dd($res, "get normal");

// 主从模式
//$config['type'] = 'ms';
//$redisClient = new RedisClient($config);
//
//$res = $redisClient->setWrite()->set('ms', 11);
//dd($res, "set ms 11");
//$res = $redisClient->setRead()->get('ms');
//dd($res, "get ms setRead");
//$res = $redisClient->get('ms');
//dd($res, "get ms");

// 哨兵模式
$config['type'] = 'sentinel';
$redisClient = new RedisClient($config);

$res = $redisClient->setWrite()->set('sentinel', 11);
dd($res, "set sentinel 11");
$res = $redisClient->setRead()->get('sentinel');
dd($res, "get sentinel setRead");
$res = $redisClient->get('sentinel');
dd($res, "get sentinel");

// 集群模式
//$config['type'] = 'cluster';
//$redisClient = new RedisClient($config);
//
//$res = $redisClient->set('cluster', 11);
//dd($res, "set cluster 11");
//$res = $redisClient->get('cluster');
//dd($res, "get cluster");
//$res = $redisClient->get('cluster2');
//dd($res, "get cluster2");
