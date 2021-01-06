package design

import "fmt"

type LRUCache struct {
	size       int
	capacity   int
	cache      map[int]*DLinkedNode
	head, tail *DLinkedNode
}

type DLinkedNode struct {
	key        int
	val        int
	prev, next *DLinkedNode
}

func NewLRUCache(capacity int) *LRUCache {
	l := &LRUCache{
		size:     0,
		capacity: capacity,
		cache:    map[int]*DLinkedNode{},
		head:     &DLinkedNode{},
		tail:     &DLinkedNode{},
	}
	l.head.next = l.tail
	l.tail.prev = l.head

	return l
}

func (l *LRUCache) Get(key int) int {
	node := l.cache[key]
	if node == nil {
		return -1
	}
	l.moveToHead(node)
	return node.val
}

func (l *LRUCache) Put(key, val int) {
	node := l.cache[key]
	if node == nil {
		newNode := &DLinkedNode{key, val, nil, nil}
		l.size++
		l.cache[key] = newNode
		l.addNode(newNode)
		if l.size > l.capacity {
			removeNode := l.removeTail()
			delete(l.cache, removeNode.key)
			l.size--
		}
	} else {
		node.key = val
		l.moveToHead(node)
	}
}

func (l *LRUCache) moveToHead(node *DLinkedNode) {
	l.removeNode(node)
	l.addNode(node)
}

func (l *LRUCache) addNode(node *DLinkedNode) {
	l.head.next.prev = node
	node.next = l.head.next
	node.prev = l.head
	l.head.next = node
}

func (l *LRUCache) removeTail() *DLinkedNode {
	node := l.tail.prev
	l.removeNode(node)
	return node
}

func (l *LRUCache) removeNode(node *DLinkedNode) {
	node.prev.next = node.next
	node.next.prev = node.next
}

//打印链表
func (l *LRUCache) PrintNode() {
	cur := l.head.next
	format := ""
	for nil != cur {
		format += fmt.Sprintf("%+v:%+v", cur.key, cur.val)
		cur = cur.next
		if nil != cur {
			format += "->"
		}
	}
	fmt.Println(format)
}
