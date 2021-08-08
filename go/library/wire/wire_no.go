package wire

import (
	"solution/library/wire/dao"
	"solution/library/wire/service"
)

func InitNoWireApp() (*service.Service, func(), error) {
	db := dao.NewDB()
	redis := dao.NewRedis()
	daoDao := dao.New(db, redis)
	serviceService := service.New(daoDao)
	return serviceService, func() {
	}, nil
}
