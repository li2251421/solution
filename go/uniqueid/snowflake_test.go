package uniqueid

import (
	"testing"
)

func TestWorker_GetId(t *testing.T) {
	worker, err := NewWorker(1)

	if err != nil {
		t.Error(err)
		return
	}

	ch := make(chan int64)
	n := 100
	// 并发n个goroutine进行id生成
	for i := 0; i < n; i++ {
		go func() {
			id := worker.GetId()
			ch <- id
		}()
	}
	defer close(ch)

	m := make(map[int64]int)
	for i := 0; i < n; i++ {
		id := <-ch
		if _, ok := m[id]; ok {
			t.Error("ID 已存在！重复ID：" + string(id))
			return
		}
		t.Log(id)
		m[id] = 1
	}
}
