package math

import "testing"

func TestCountPrimes(t *testing.T) {
	n := 10
	res := countPrimes(n)
	t.Log(res)
}
