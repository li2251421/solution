package zookeeper

import (
	"errors"
	"github.com/samuel/go-zookeeper/zk"
	"log"
	"sort"
	"time"
)

/**
分布式锁，堵塞调用
通过创建顺序临时节点构建锁队列，每次只能队列中的第一个节点才能拿锁，用完删除节点表示删除锁
每个节点监听上一个节点的存在性事件，上一个节点删除后获取锁

优点：不需要关心锁超时，获取锁会按照加锁的顺序，是公平锁
缺点：ZK需要额外维护，性能跟MySQL差不多(索引的创建、删除，查找上一个节点)

参考：https://github.com/apache/curator
todo: 非堵塞、可重入性、性能优化(getWatchIndex算法)
*/

type IZkLock interface {
	CreateLock() error
	TryLock() error
	UnLock() error
}

type ZkLock struct {
	LockRootPath string // 根路径

	curPath string // 当前路径
	conn    *zk.Conn
}

func NewZkLock(lockPath string, hosts []string) (*ZkLock, error) {
	l := &ZkLock{LockRootPath: lockPath}

	conn, _, err := zk.Connect(hosts, 5*time.Second)
	if err != nil {
		return nil, err
	}

	l.conn = conn

	return l, nil
}

func (l *ZkLock) CreateLock() error {
	isExists, _, err := l.conn.Exists(l.LockRootPath)
	if err != nil {
		return err
	}
	if !isExists {
		_, err = l.conn.Create(l.LockRootPath, nil, 0, zk.WorldACL(zk.PermAll))
		if err != nil {
			return err
		}
	}
	l.curPath, err = l.conn.Create(l.LockRootPath+"/", nil, zk.FlagEphemeral|zk.FlagSequence, zk.WorldACL(zk.PermAll))
	if err != nil {
		return err
	}
	log.Println("创建临时锁节点: ", l.curPath)

	return nil
}

/**
获取锁
return nil 表示获取成功
*/

func (l *ZkLock) TryLock() error {
	lockPaths, _, err := l.conn.Children(l.LockRootPath)
	if err != nil {
		return err
	}

	if len(lockPaths) <= 0 {
		return errors.New("empty nodes")
	}

	sort.Strings(lockPaths)
	log.Println("RootPathChildren: ", lockPaths)

	log.Println("当前节点：", l.curPath)
	log.Println("锁队列第一个节点：", l.LockRootPath+"/"+lockPaths[0])

	if l.LockRootPath+"/"+lockPaths[0] == l.curPath {
		log.Println("当前节点获取到锁")
		return nil
	}
	watchIndex, err := l.getWatchIndex(lockPaths, l.curPath)
	if err != nil {
		return err
	}
	return l.waitLock(lockPaths[watchIndex])
}

// 获取监视的节点(当前节点的上一个节点)
func (l *ZkLock) getWatchIndex(lockPaths []string, curPath string) (int, error) {
	// 查找当前节点的index
	for i := 0; i < len(lockPaths); i++ {
		if l.LockRootPath+"/"+lockPaths[i] == curPath {
			return i - 1, nil
		}
	}
	return -1, errors.New("watchIndex not found")
}

/**
监听等待锁释放(上一个节点删除)
return nil 表示获取锁成功
*/
func (l *ZkLock) waitLock(path string) error {
	watchPath := l.LockRootPath + "/" + path
	log.Printf("监听者：%v, 监听节点：%v\n", l.curPath, watchPath)
	isExists, _, nodeEvent, err := l.conn.ExistsW(watchPath)
	if err != nil {
		return err
	}
	if !isExists {
		log.Printf("获取锁: %v, 监听节点不存在: %v\n", l.curPath, watchPath)
		return nil
	}
	for {
		select {
		case event := <-nodeEvent:
			{
				if event.Type == zk.EventNodeDeleted {
					log.Printf("获取锁: %v，监控节点下线: %v\n", l.curPath, watchPath)
					return nil
				}
			}
		}
	}
	return nil
}

// 释放锁，删除自己
func (l *ZkLock) UnLock() error {
	log.Println("释放锁：", l.curPath)

	_, stat, err := l.conn.Get(l.curPath)
	if err != nil {
		return err
	}

	return l.conn.Delete(l.curPath, stat.Version)
}
