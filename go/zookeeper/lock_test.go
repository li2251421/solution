package zookeeper

import (
	"log"
	"sync"
	"testing"
	"time"
)

var n = 0

func TestZkLock(t *testing.T) {
	wg := sync.WaitGroup{}
	count := 10
	wg.Add(count)

	for i := 0; i < count; i++ {
		go func() {
			Lock(addN)
			wg.Done()
		}()
	}
	wg.Wait()
	t.Log("n: ", n)
}

func Lock(callback func()) {
	zkLock, err := NewZkLock("/locktest", []string{"127.0.0.1:2181"})
	if err != nil {
		log.Fatal("NewZkLock-err: ", err)
	}
	err = zkLock.CreateLock()
	if err != nil {
		log.Fatal("CreateLock-err: ", err)
	}
	err = zkLock.TryLock()
	if err != nil {
		log.Fatal("TryLock-err: ", err)
	}

	callback()

	err = zkLock.UnLock()
	if err != nil {
		log.Fatal("UnLock-err: ", err)
	}
}

func addN() {
	time.Sleep(10 * time.Millisecond)
	n++
}
