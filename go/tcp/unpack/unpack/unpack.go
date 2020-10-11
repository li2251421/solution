package unpack

import (
	"encoding/binary"
	"errors"
	"io"
)

const Msg_header = "heng"

// msg_header+content_len+content
func Encode(bytesBuffer io.Writer, content string) error {
	// 网络协议层操作二进制，约定使用大端序BigEndian
	if err := binary.Write(bytesBuffer, binary.BigEndian, []byte(Msg_header)); err != nil {
		return err
	}
	// 注意Write方法中data支持的类型，此处限制最长int32
	clen := int32(len([]byte(content)))
	if err := binary.Write(bytesBuffer, binary.BigEndian, clen); err != nil {
		return err
	}
	if err := binary.Write(bytesBuffer, binary.BigEndian, []byte(content)); err != nil {
		return err
	}
	return nil
}

func Decode(bytesBuffer io.Reader) (bodyBuf []byte, err error) {
	magicBuf := make([]byte, len(Msg_header))
	if _, err := io.ReadFull(bytesBuffer, magicBuf); err != nil {
		return nil, err
	}
	if string(magicBuf) != Msg_header {
		return nil, errors.New("msg_header error")
	}
	// content长度用int32存储，占4个字节
	lengthBuf := make([]byte, 4)
	if _, err := io.ReadFull(bytesBuffer, lengthBuf); err != nil {
		return nil, err
	}
	// 读取大端序数据(content长度)
	length := binary.BigEndian.Uint32(lengthBuf)
	bodyBuf = make([]byte, length)
	if _, err := io.ReadFull(bytesBuffer, bodyBuf); err != nil {
		return nil, err
	}
	return bodyBuf, nil
}
