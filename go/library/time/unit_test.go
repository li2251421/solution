package time

import (
	"fmt"
	"testing"
	"time"
)

func TestTime(t *testing.T) {
	seconds := 100000
	fmt.Println(time.Duration(seconds) * time.Second)

	t0 := time.Now()
	fmt.Println(t0)
	func() { time.Sleep(time.Second) }()
	t1 := time.Now()
	fmt.Printf("The call took %v to run.\n", t1.Sub(t0))

	parseT, _ := time.Parse("20060102", "20210817")
	loc, _ := time.LoadLocation("PRC")
	parseLocT, _ := time.ParseInLocation("20060102", "20210817", loc)
	fmt.Println(parseT, parseLocT)
	fmt.Println(parseT.Location(), parseLocT.Location())
}
