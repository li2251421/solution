package math

import "testing"

func TestCountPrimes(t *testing.T) {
	n := 10
	res := countPrimes(n)
	t.Log(res)
}

func TestTrailingZeroes(t *testing.T) {
	n := 10
	res := trailingZeroes(n)
	t.Log(res)
}
