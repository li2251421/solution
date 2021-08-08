package service

import "solution/library/wire/dao"

type Service struct {
	dao *dao.Dao
}

func New(dao *dao.Dao) *Service {
	return &Service{
		dao: dao,
	}
}
