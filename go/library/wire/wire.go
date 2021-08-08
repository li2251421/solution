// +build wireinject
// The build tag makes sure the stub is not built in the final build.

package wire

import (
	"github.com/google/wire"
	"solution/library/wire/dao"
	"solution/library/wire/service"
)

//go:generate wire
func InitWireApp() (*service.Service, func(), error) {
	panic(wire.Build(DBSet, dao.New, service.New))
}

var DBSet = wire.NewSet(dao.NewDB, dao.NewRedis)
