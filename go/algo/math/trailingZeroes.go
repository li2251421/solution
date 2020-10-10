package math

/**
输入一个非负整数 n，请你计算阶乘 n! 的结果末尾有几个 0。

0出现的原因？  2 * 5 才会出现
问题转化为 n!最多可以分解成多少个 2 和 5
因子2肯定比5多，所以问题转化为: n!最多可以分解成多少个因子5
 */

func trailingZeroes(n int) int {
	res := 0
	for d := n; d/5 > 0; d = d / 5 {
		res += d / 5
	}
	return res
}
