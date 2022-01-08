<?php

namespace BitrixModels\Repository;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use BitrixModels\Entity\BaseModel;
use BitrixFilterBuilder\Filter;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;

class HighloadRepository extends BaseRepository
{
    protected $entityDataClass;

    protected $lastError = '';

    public function __construct($class)
    {
        parent::__construct($class);

        $this->hlblock = HL\HighloadBlockTable::getById($this->getClassModel()::iblockId())->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($this->hlblock);
        $this->entityDataClass = $entity->getDataClass();
    }

    public function findById($id): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('ID', $id);

        return $this->findOneByFilter($filter);
    }

    public function findByExtId($extId): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('XML_ID', $extId);

        return $this->findOneByFilter($filter);
    }

    public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel
    {
        $filter = $this->getResultFilter($filter);

        $params = [
            'select' => ['*'],
            'filter' => $filter->getResult(),
            'limit' => 1,
            'offset' => 0
        ];

        if ($sort) {
            $params['order'] = $sort->getResult();
        }

        $rsData = $this->entityDataClass::getList($params);

        if ($element = $rsData->Fetch()) {
            $result = $this->getNewEntity()->mapData($element);
        }

        return $result;
    }

    public function countByFilter(Filter $filter = null): int
    {
        $filter = $this->getResultFilter($filter);

        $rsData = $this->entityDataClass::getList([
            "select" => ["*"],
            "order" => ["ID" => "ASC"],
            "filter" => $filter->getResult()
        ]);

        return (int)$rsData->getSelectedRowsCount();
    }

    public function findByFilter(Select $select = null, Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult
    {
        $filter = $this->getResultFilter($filter);

        $params = [
            'select' => ['*'],
            'filter' => $filter->getResult(),
            'limit' => $count,
            'offset' => ($page - 1) * $count,
        ];

        if ($sort) {
            $params['order'] = $sort->getResult();
        }

        $dbItems = $this->entityDataClass::getList($params);

        while ($element = $dbItems->Fetch()) {
            $list[] = $this->getNewEntity()->mapData($element);
        }

        $pagination = new Pagination($page, $count, ceil($dbItems->getSelectedRowsCount() / $count), $dbItems->getSelectedRowsCount());

        $result = new ListResult();
        $result->setList($list);
        $result->setPagination($pagination);

        return $result;
    }

    public function add($data)
    {
        $result = $this->entityDataClass::add($data);
        return $result;
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function update($id, $data = [])
    {
        $result = $this->entityDataClass::update($id, $data);
        return $result;
    }
}
