<?php

namespace BitrixModels\Repository;

use BitrixModels\Model\Filter;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;

abstract class BaseRepository
{
    const MAX_RESULT  = 1000000000;

    protected $class = null;

    protected string $lastError = '';

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

    abstract public function findAllByFilter(Select $select = null, Filter $filter = null, Sort $sort = null): ListResult;

    abstract public function findAll(Select $select = null, Sort $sort = null): ListResult;

    abstract public function findByFilter(Select $select = null, Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult;

    abstract public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel;

    abstract public function countByFilter(Filter $filter = null): int;

    abstract public function findById($id): ?BaseModel;

    abstract public function findByExtId($extId): ?BaseModel;

    abstract public function add(array $data = [], array $properties = []): int|false;

    abstract public function update(int $id, array $data = [], array $properties = []): bool;

    public function refresh(BaseModel &$model): BaseModel
    {
        $model = $this->findById($model->getId()->getValue());
        return $model;
    }

    public function getTagIblock()
    {
        return static::class . static::getClassModel()::iblockId();
    }

    /**
     * @return string
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * @param string $lastError
     */
    public function setLastError(string $lastError): void
    {
        $this->lastError = $lastError;
    }
}
