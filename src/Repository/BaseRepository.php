<?php

namespace BitrixModels\Repository;

use BitrixModels\Entity\BaseModel;
use BitrixFilterBuilder\Filter;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Sort;

abstract class BaseRepository
{
    protected $class = null;

    public function __construct($class = null)
    {
        if ($class) {
            $this->class = $class;
        }
    }

    public function getClassModel()
    {
        return $this->class;
    }

    public function getNewEntity(): BaseModel
    {
        $class = self::getClassModel();

        return new $class();
    }

    protected function getResultFilter(Filter $filter = null): Filter
    {
        if (!$filter) {
            $filter = new Filter();
        }

        return $filter;
    }

    abstract public function findByFilter(Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult;

    abstract public function findFieldByFilter($fields, Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult;

    abstract public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel;

    abstract public function countByFilter(Filter $filter = null): int;

    abstract public function findById($id): ?BaseModel;

    abstract public function findByExtId($extId): ?BaseModel;

    public function save(BaseModel &$model): ?BaseModel
    {
        if (static::getClassModel() != get_class($model)) {
            throw new \ErrorException();
        }

        return null;
    }

    public function getTagIblock()
    {
        return static::class . static::getClassModel()::iblockId();
    }
}