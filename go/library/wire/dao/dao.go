package dao

type Dao struct {
	db    *DB
	redis *Redis
}

func New(db *DB, redis *Redis) *Dao {
	return &Dao{
		db:    db,
		redis: redis,
	}
}
