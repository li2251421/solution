package heap

/**
堆
特性
	完全二叉树(除了最后一层，其他层的节点个数都是满的，最后一层的节点靠左排列)
 	每个节点的值都必须大于等于(大顶堆)/小于等于(小顶堆)其子树中每个节点的值
堆化：从下往下/从下往上
应用：优先级队列(合并有序小文件/高性能定时器)、TopK、求中位数
*/

type Heap struct {
	a        []int // 数组存储,下标从0开始
	capacity int   // 容量
	count    int   // 当前个数
}

func NewHeap(capacity int) *Heap {
	return &Heap{
		a:        make([]int, capacity),
		capacity: capacity,
		count:    0,
	}
}

func (heap *Heap) Insert(data int) {
	if heap.capacity == heap.count {
		return
	}
	heap.count++
	heap.a[heap.count-1] = data

	// 插入到数组结尾，从结尾自下往上堆化
	heap.heapifyDownToUp(heap.count - 1)
}

func (heap *Heap) RemoveMax() int {
	if heap.count == 0 {
		return 0
	}
	max := heap.a[0]
	// 将最后一个元素移到堆顶,从堆顶从下往下堆化
	heap.a[0] = heap.a[heap.count-1]
	heap.count--
	heap.a = heap.a[:heap.count]
	heap.heapifyUpToDown(0)
	return max
}

// 从top下标自上往下堆化
func (heap *Heap) heapifyUpToDown(top int) {
	for {
		maxPos := top
		left := top*2 + 1
		right := top*2 + 2
		if left < heap.count && heap.a[left] > heap.a[top] {
			maxPos = left
		}
		if right < heap.count && heap.a[right] > heap.a[maxPos] {
			maxPos = right
		}
		if maxPos == top {
			break
		}
		heap.a[top], heap.a[maxPos] = heap.a[maxPos], heap.a[top]
		top = maxPos
	}
}

// 从bottom下标自下往上堆化
func (heap *Heap) heapifyDownToUp(bottom int) {
	parent := (bottom - 1) / 2
	for parent >= 0 && heap.a[parent] < heap.a[bottom] {
		heap.a[bottom], heap.a[parent] = heap.a[parent], heap.a[bottom]
		bottom = parent
		parent = (bottom - 1) / 2
	}
}
