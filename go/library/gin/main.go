package main

import (
	"encoding/json"
	"fmt"
	"github.com/gin-gonic/gin"
	"github.com/gin-gonic/gin/binding"
	"log"
	"net/http"
)

type A struct {
	a1     int64
	prefix string
	*log.Logger
}

type B struct {
	b1 int64
	b2 int64
}

func main() {

	//str := "{\"1\":\"123\",\"2\":\"\"}"
	//var m map[string]string
	//err := json.Unmarshal([]byte(str), &m)
	//fmt.Println(err, m)
	//return
	router := gin.Default()
	router.GET("/ping", func(c *gin.Context) {
		c.JSON(200, gin.H{
			"message": "pong",
		})
	})
	/**
	  POST /post?id=1234&page=1 HTTP/1.1
	  Content-Type: application/x-www-form-urlencoded

	  name=manu&message=this_is_great
	*/
	router.POST("/post", func(c *gin.Context) {

		id := c.Query("id")
		page := c.DefaultQuery("page", "0")
		name := c.PostForm("name")
		message := c.PostForm("message")

		fmt.Printf("id: %s; page: %s; name: %s; message: %s", id, page, name, message)
	})

	router.GET("/form", func(c *gin.Context) {
		var v = new(struct {
			Oid  int64   `form:"oid"`
			Oids []int64 `form:"oids,split"`
		})
		fmt.Println(c.ContentType())
		fmt.Println(c.PostFormArray("oids"))
		if err := c.Bind(v); err != nil {
			c.JSON(200, gin.H{
				"error": err.Error(),
			})
			return
		}
		c.JSON(200, gin.H{
			"oid":  v.Oid,
			"oids": v.Oids,
		})
	})

	router.POST("/json", func(c *gin.Context) {
		req := &JsonReq{}
		c.BindWith(req, binding.JSON)
		res, _ := json.Marshal(req.Test)
		c.JSON(200, gin.H{
			"test": req.Test,
			"res":  string(res),
		})
	})

	router.Run(":8080")
}

type JsonReq struct {
	Test map[string]string `json:"test"`
}

type Test struct {
	Arc    string `json:"arc"`
	Column string `json:"column"`
}

type formA struct {
	Foo string `json:"foo" xml:"foo" binding:"required"`
}

type formB struct {
	Bar string `json:"bar" xml:"bar" binding:"required"`
}

func SomeHandler(c *gin.Context) {
	objA := formA{}
	objB := formB{}
	// c.ShouldBind 使用了 c.Request.Body，不可重用。
	if errA := c.ShouldBind(&objA); errA == nil {
		c.String(http.StatusOK, `the body should be formA`)
		// 因为现在 c.Request.Body 是 EOF，所以这里会报错。
	} else if errB := c.ShouldBind(&objB); errB == nil {
		c.String(http.StatusOK, `the body should be formB`)
	} else {
	}

	// 读取 c.Request.Body 并将结果存入上下文。
	if errA := c.ShouldBindBodyWith(&objA, binding.JSON); errA == nil {
		c.String(http.StatusOK, `the body should be formA`)
		// 这时, 复用存储在上下文中的 body。
	} else if errB := c.ShouldBindBodyWith(&objB, binding.JSON); errB == nil {
		c.String(http.StatusOK, `the body should be formB JSON`)
		// 可以接受其他格式
	} else if errB2 := c.ShouldBindBodyWith(&objB, binding.XML); errB2 == nil {
		c.String(http.StatusOK, `the body should be formB XML`)
	} else {
	}

}
