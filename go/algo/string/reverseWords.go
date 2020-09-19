package string

/**
O(1)空间复杂度实现单词反转 www.baidu.com => com.baidu.www
 */

func reverseWords(s []byte) string {
	reverse(s, 0, len(s)-1)
	start := 0
	for i := 0; i < len(s); i++ {
		if s[i] == '.' {
			reverse(s, start, i-1)
			start = i + 1
		}
	}
	return string(s)
}

func reverse(s []byte, start, end int) {
	for start < end {
		s[start], s[end] = s[end], s[start]
		start++
		end--
	}
}
