package heap

import "testing"

func TestHeap(t *testing.T) {
	h := NewHeap(5)
	h.Insert(1)
	h.Insert(2)
	h.Insert(3)
	h.Insert(4)
	h.Insert(5)
	t.Log(h.a)
	h.RemoveMax()
	t.Log(h.a)
	h.RemoveMax()
	t.Log(h.a)
}