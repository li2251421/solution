package design

import "testing"

func TestLRUCache(t *testing.T) {
	l := NewLRUCache(3)
	l.Put(1, 11)
	l.Put(2, 22)
	l.Put(3, 33)
	l.PrintNode()
	l.Put(4, 44)
	l.PrintNode()
	l.Get(2)
	l.PrintNode()
}
