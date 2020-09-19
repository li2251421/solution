package sort

import "testing"

func TestQuickSort(t *testing.T) {
	a := []int{1, 5, 2, 8, 4, 3}
	quickSort(a)
	t.Log(a)
}

func TestHeapSort(t *testing.T) {
	a := []int{3, 1, 8, 2, 5, 9}
	heapSort(a)
	t.Log(a)
}
