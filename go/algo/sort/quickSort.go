package sort

/**
快速排序
时间复杂度：O(nlogn)，极端情况(有序数组分区点选择最后一个元素)退化成O(n^2)
空间复杂度：O(1) 原地排序
稳定排序：否

分治分区思想，自上而下
*/

func quickSort(a []int) {
	n := len(a)
	if n <= 1 {
		return
	}
	quickSortC(a, 0, n-1)
}

func quickSortC(a []int, start, end int) {
	if start >= end {
		return
	}
	i := partition(a, start, end)
	quickSortC(a, start, i-1)
	quickSortC(a, i+1, end)
}

// 寻找分区点，i左边为小于分区点的元素，右边为大于分区点的元素
func partition(a []int, start, end int) int {
	pivot := a[end] // 分区点选择需要设置一定的算法
	i := start
	for j := start; j < end; j++ {
		if a[j] < pivot {
			// 小于分区点时，ij交换元素，i++，保证i左边的元素小于分区点
			if i != j {
				a[i], a[j] = a[j], a[i]
			}
			i++
		}
	}
	// 最后将i和分区点交换
	a[i], a[end] = a[end], a[i]
	return i
}
