package sort

/**
归并排序
时间复杂度：O(nlogn)
空间复杂度：O(n) 非原地排序
稳定排序：是

分治思想，自下至上
*/

func mergeSort(a []int) {
	n := len(a)
	if n <= 1 {
		return
	}
	mergeSortC(a, 0, n-1)
}

func mergeSortC(a []int, start, end int) {
	if start >= end {
		return
	}
	mid := start + (end-start)/2
	mergeSortC(a, start, mid)
	mergeSortC(a, mid+1, end)
	merge(a, start, mid, end)
}

func merge(a []int, start, mid, end int) {
	tmpArr := make([]int, end-start+1)

	i, j := start, mid+1
	k := 0
	for ; i <= mid && j <= end; k++ {
		// <= 保证是稳定排序
		if a[i] <= a[j] {
			tmpArr[k] = a[i]
			i++
		} else {
			tmpArr[k] = a[j]
			j++
		}
	}
	for ; i <= mid; k++ {
		tmpArr[k] = a[i]
		i++
	}
	for ; j <= end; k++ {
		tmpArr[k] = a[j]
		j++
	}
	// 将结果拷贝回原数组
	copy(a[start:end+1], tmpArr)
}
