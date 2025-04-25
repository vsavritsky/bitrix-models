<?php

namespace BitrixModels\Repository;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\Filter;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;
use BitrixModels\QueryBuilder\ElementQueryBuilder;
use BitrixModels\QueryBuilder\HighloadQueryBuilder;

class HighloadRepository extends BaseRepository
{
    public function getQueryBuilder(): HighloadQueryBuilder
    {
        return new HighloadQueryBuilder(self::getClassModel());
    }

    protected $entityDataClass;

    public function __construct($class = null)
    {
        parent::__construct($class);

        $entity = HL\HighloadBlockTable::compileEntity(HL\HighloadBlockTable::getById($this->getClassModel()::iblockId())->fetch());
        $this->entityDataClass = $entity->getDataClass();
    }

    public function findById($id): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('ID', $id);

        $select = new Select();
        $select->withAllProperties();

        return $this->getQueryBuilder()->filter($filter)->select($select)->getOneResult();
    }

    public function findByExtId($extId): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('UF_XML_ID', $extId);

        $select = new Select();
        $select->withAllProperties();

        return $this->getQueryBuilder()->filter($filter)->select($select)->getOneResult();
    }

    public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel
    {
		$select = new Select();
        $select->withAllProperties();

        return $this->getQueryBuilder()->filter($filter)->select($select)->sort($sort)->getOneResult();
    }

    public function countByFilter(Filter $filter = null): int
    {
        if (!$filter) {
            $filter = new Filter();
        }

        return $this->getQueryBuilder()->filter($filter)->getCountResult();
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
            $select->withAllProperties();
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
            $select->withAllProperties();
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
            $select->withAllProperties();
        }

        return $this->getQueryBuilder()->select($select)->filter($filter)->sort($sort)->count(self::MAX_RESULT)->getResult();
    }

    public function add($data)
    {
        $result = $this->entityDataClass::add($data);
        return $result;
    }
    public function update($id, $data = [])
    {
        $result = $this->entityDataClass::update($id, $data);
        return $result;
    }
}
