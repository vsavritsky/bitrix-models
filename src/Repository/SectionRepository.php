<?php

namespace BitrixModels\Repository;

use BitrixModels\Model\Filter;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\Iblock;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;
use BitrixModels\QueryBuilder\SectionQueryBuilder;
use CIBlock;
use CIBlockElement;

class SectionRepository extends BaseRepository
{
    public function getQueryBuilder(): SectionQueryBuilder
    {
        return new SectionQueryBuilder(self::getClassModel());
    }

    public function countByFilter(Filter $filter = null): int
    {
        if (!$filter) {
            $filter = new Filter();
        }

        return $this->getQueryBuilder()->filter($filter)->getCountResult();
    }

    public function findByExtId($extId): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('XML_ID', $extId);

        return $this->getQueryBuilder()->filter($filter)->getOneResult();
    }

    public function findById($id): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('ID', $id);

        $select = new Select();
        $select->withProperties();
        $select->withSeo();

        return $this->getQueryBuilder()->filter($filter)->select($select)->getOneResult();
    }

    public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel
    {
        $select = new Select();
        $select->withProperties();
        $select->withSeo();

        return $this->getQueryBuilder()->filter($filter)->select($select)->sort($sort)->getOneResult();
    }

    public function findByFilter(Select $select = null, Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult
    {
        if (!$filter) {
            $filter = new Filter();
        }

        if (!$sort) {
            $sort = new Sort();
        }

        if (!$select) {
            $select = new Select();
            $select->withProperties();
        }

        return $this->getQueryBuilder()->select($select)->filter($filter)->sort($sort)->page($page)->count($count)->getResult();
    }

    public function findAllByFilter(Select $select = null, Filter $filter = null, Sort $sort = null): ListResult
    {
        if (!$filter) {
            $filter = new Filter();
        }

        if (!$sort) {
            $sort = new Sort();
        }

        if (!$select) {
            $select = new Select();
            $select->withProperties();
        }

        return $this->getQueryBuilder()->select($select)->filter($filter)->sort($sort)->count(self::MAX_RESULT)->getResult();
    }

    public function findAll(Select $select = null, Sort $sort = null): ListResult
    {
        $filter = new Filter();

        if (!$sort) {
            $sort = new Sort();
        }

        if (!$select) {
            $select = new Select();
            $select->withProperties();
        }

        return $this->getQueryBuilder()->select($select)->filter($filter)->sort($sort)->count(self::MAX_RESULT)->getResult();
    }

    public function save(BaseModel &$model): ?BaseModel
    {
        return null;
    }

    public function add($fields = [], $properties = [])
    {
        return false;
    }

    public function update($id, $fields = [], $properties = [])
    {
        return null;
    }
}
