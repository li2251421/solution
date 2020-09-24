package math

/**
统计所有小于非负整数 n 的质数的数量

[2,sqrt(n)]区间没有可整除因子，可以直接判断n是质数
2是质数，2*2=4，2*3=6，2*4=8...都不可能是质数
*/

func countPrimes(n int) int {
	isPrim := make(map[int]bool)
	for i := 2; i < n; i++ {
		isPrim[i] = true // 初始化为质数
	}
	// 循环2-sqrt(n)区间
	for i := 2; i*i < n; i++ {
		if isPrim[i] {
			// 从2*i开始会有冗余计算,如 2*3 和 3*2
			for j := i * i; j < n; j = j + i {
				isPrim[j] = false
			}
		}
	}
	count := 0
	for i := 2; i < n; i++ {
		if isPrim[i] {
			count++
		}
	}
	return count
}
