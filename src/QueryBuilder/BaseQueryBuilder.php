<?php

namespace BitrixModels\QueryBuilder;

use BitrixModels\Model\Filter;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;

abstract class BaseQueryBuilder
{
    protected ?Select $select = null;
    protected ?Filter $filter = null;
    protected ?Sort $sort = null;
    protected ?Pagination $pagination = null;
    protected int $cacheTime = 0;

    public function __construct()
    {
        $this->select = new Select(['ID']);
        $this->filter = new Filter();
        $this->sort = new Sort();
        $this->pagination = new Pagination();
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

    protected function getResultSort(Sort $sort = null): Sort
    {
        if (!$sort) {
            $sort = new Sort();
        }

        return $sort;
    }

    protected function getCacheId(): string
    {
        return md5(serialize([$this->select, $this->filter, $this->sort, $this->pagination]));
    }

    public function filter(Filter $filter = null): self
    {
        $this->filter = $this->getResultFilter($filter);
        return $this;
    }

    public function sort(Sort $sort = null) : self
    {
        $this->sort = $this->getResultSort($sort);;
        return $this;
    }

    public function select(Select $select) : self
    {
        $this->select = $select;

        return $this;
    }

    public function page($page = 1) : self
    {
        $this->pagination->setCurrentPage($page);

        return $this;
    }

    public function count($count = 10) : self
    {
        $this->pagination->setPerPage($count);

        return $this;
    }

    public function cache($time = 3600) : self
    {
        if ($time) {
            $this->cacheTime = $time;
        }

        return $this;
    }

    abstract public function getResult(): ListResult;
    abstract public function getOneResult(): ?BaseModel;
    abstract public function getCountResult(): int;
}
