package sort

/**
堆排序
时间复杂度：O(nlogn)
空间复杂度：O(1) 原地排序
稳定排序：否

堆排序数据访问方式不是顺序访问，对cpu缓存不友好，快读排序是顺序访问
堆排序数据交换次数多于快速排序
*/

func heapSort(a []int) {
	n := len(a)
	// 先将数组建成大顶堆
	buildHeap(a)

	// 利用堆删除元素的技巧，将堆顶最大值与数组结尾元素交换，然后从顶部自上而下堆化
	k := n - 1
	for k >= 0 {
		a[0], a[k] = a[k], a[0]
		heapifyUpToDown(a, 0, k)
		k--
	}
}

// 建堆
func buildHeap(a []int) {
	n := len(a)
	// 从第一个非叶子节点(倒数第二层第一个节点 n/2-1)自上而下依次堆化
	for i := n/2 - 1; i >= 0; i-- {
		heapifyUpToDown(a, i, n)
	}
}

func heapifyUpToDown(a []int, top, count int) {
	for {
		maxPos := top
		left := top*2 + 1
		right := top*2 + 2
		if left < count && a[left] > a[top] {
			maxPos = left
		}
		if right < count && a[right] > a[maxPos] {
			maxPos = right
		}
		if maxPos == top {
			break
		}
		a[top], a[maxPos] = a[maxPos], a[top]
		top = maxPos
	}
}
