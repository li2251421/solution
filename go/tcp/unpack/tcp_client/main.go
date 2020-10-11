package main

import (
	"fmt"
	"net"
	"solution/tcp/unpack/unpack"
)

func main() {
	addr := "0.0.0.0:9000"

	conn, err := net.Dial("tcp", addr)
	defer conn.Close()

	if err != nil {
		fmt.Printf("connect failed, err: %v\n", err.Error())
		return
	}

	fmt.Printf("dial success, addr: %v\n", addr)

	data := "hello world!"
	fmt.Printf("send request, data: %v\n", data)
	unpack.Encode(conn, data)
}
