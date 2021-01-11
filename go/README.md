# 通用解决方案-Go

## 项目运行
### Go环境
```shell
wget -O https://golang.org/dl/go1.15.3.linux-amd64.tar.gz
tar -C /usr/local -zxvf go1.15.3.linux-amd64.tar.gz
vim /etc/profile

export GOROOT=/usr/local/go
export GOPATH=/usr/local/goprojects
export PATH=$PATH:$GOROOT/bin
source /etc/profile

go env -w GO111MODULE=on
go env -w GOPROXY=https://goproxy.cn,direct
```
## 项目实现
### uniqueid-分布式唯一ID
- [Snowflake](https://github.com/li2251421/solution/blob/master/go/uniqueid/snowflake.go)

### tcp
- [unpack](https://github.com/li2251421/solution/tree/master/go/tcp/unpack) 
msg_header+content_len+content解决粘包/拆包问题

### zookeeper
- [lock](https://github.com/li2251421/solution/blob/master/go/zookeeper/lock.go) 
分布式锁

### load_balance-负载均衡算法(todo)
- [random]() 
随机
- [round_robin]() 
轮询
- [weight_round_robin]() 
加权轮询
- [consistent_hash]()
一致性哈希

### algo-算法
- [array](https://github.com/li2251421/solution/tree/master/go/algo/array) 
数组
- [string](https://github.com/li2251421/solution/tree/master/go/algo/string) 
字符串
- [linkedlist](https://github.com/li2251421/solution/tree/master/go/algo/linkedlist) 
链表
- [stack](https://github.com/li2251421/solution/tree/master/go/algo/stack) 
栈
- [queue](https://github.com/li2251421/solution/tree/master/go/algo/queue) 
队列
- [heap](https://github.com/li2251421/solution/tree/master/go/algo/heap) 
堆
- [sort](https://github.com/li2251421/solution/tree/master/go/algo/sort) 
排序
- [dp](https://github.com/li2251421/solution/tree/master/go/algo/dp) 
动态规划
- [double_pointer](https://github.com/li2251421/solution/tree/master/go/algo/double_pointer) 
双指针
- [sliding_window](https://github.com/li2251421/solution/tree/master/go/algo/sliding_window) 
滑动窗口
- [backtrack](https://github.com/li2251421/solution/tree/master/go/algo/traceback) 
回溯算法
- [math](https://github.com/li2251421/solution/tree/master/go/algo/math) 
数学问题
- [design](https://github.com/li2251421/solution/tree/master/go/algo/design) 
设计