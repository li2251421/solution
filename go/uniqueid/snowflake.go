package uniqueid

import (
	"errors"
	"fmt"
	"sync"
	"time"
)

const (
	epoth int64 = 1596284460 * 1000 // 起始时间戳,毫秒

	sequence_bits uint8 = 12                           // 序号部分，12bit
	sequence_max  int64 = 1<<uint64(sequence_bits) - 1 // 序号最大值

	worker_bits uint8 = 10                         // 机器节点，10bit
	worker_max  int64 = 1<<uint64(worker_bits) - 1 // 机器节点最大值

	time_offset   = worker_bits + sequence_bits // 时间戳部分左偏移量
	worker_offset = sequence_bits               // 机器节点部分左偏移量
)

type Worker struct {
	mu       sync.Mutex // 互斥锁
	lastTime int64      // 上次ID生成时间戳,毫秒
	workerId int64      // 机器节点
	sequence int64      // 序号
}

func NewWorker(workerId int64) (*Worker, error) {
	if workerId < 0 || workerId > worker_max {
		return nil, errors.New("Worker Id 超出范围")
	}
	return &Worker{
		lastTime: 0,
		workerId: workerId,
		sequence: 0,
	}, nil
}

func (w *Worker) GetId() int64 {
	w.mu.Lock()
	defer w.mu.Unlock()

	now := getMilliSecond()
	// 时钟回拨问题，借用上次时间戳并报警通知
	if w.lastTime > now {
		fmt.Printf("workerId: %+v, now: %+v, lastTime: %+v, err: %+v\n", w.workerId, now, w.lastTime, "clock is turn back")
		now = w.lastTime
	}
	if w.lastTime == now {
		w.sequence++
		if w.sequence > sequence_max {
			// 当前毫秒序号用完，等待一下毫秒生成
			for now <= w.lastTime {
				now = getMilliSecond()
			}
		}
	} else {
		w.sequence = 0
	}
	w.lastTime = now

	id := (now-epoth)<<time_offset | w.workerId<<worker_offset | w.sequence
	fmt.Printf("lastTime: %+v, sequence: %+v\n", w.lastTime, w.sequence)
	return id
}

func getMilliSecond() int64 {
	return time.Now().UnixNano() / 1e6
}
