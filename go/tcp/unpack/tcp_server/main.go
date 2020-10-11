package main

import (
	"fmt"
	"net"
	"solution/tcp/unpack/unpack"
)

func main() {
	addr := "0.0.0.0:9000"
	listener, err := net.Listen("tcp", addr)

	if err != nil {
		fmt.Printf("listen failed, err: %v\n", err.Error())
		return
	}
	fmt.Printf("listen success, addr: %v\n", addr)

	for {
		conn, err := listener.Accept()
		if err != nil {
			fmt.Printf("accept failed, err: %v\n", err.Error())
			continue
		}
		go process(conn)
	}
}

func process(conn net.Conn) {
	defer conn.Close()
	for {
		buf, err := unpack.Decode(conn)
		if err != nil {
			fmt.Printf("read from connect failed, err: %v\n", err.Error())
			break
		}
		fmt.Printf("receive from client, data: %v\n", string(buf))
	}
}
