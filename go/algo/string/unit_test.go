package string

import "testing"

func TestReverseWords(t *testing.T) {
	s := "www.baidu.com"
	res := reverseWords([]byte(s))
	t.Log(res)
}
