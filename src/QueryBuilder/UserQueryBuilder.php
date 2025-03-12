<?php

namespace BitrixModels\QueryBuilder;

use Bitrix\Main\Data\Cache;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use CUser;

class UserQueryBuilder extends BaseQueryBuilder
{
    protected $fields = [];
    protected $entityDataClass;

    protected $class;

    public function __construct($class)
    {
        parent::__construct();

        $this->class = $class;
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

    public function getResult(): ListResult
    {
        $list = [];

        $res = CUser::GetList(
            $this->sort->getSortBy(),
            $this->sort->getSortDirection(),
            $this->getResultFilter($this->filter)->getResult(),
            [
                'SELECT' => $this->select->getResult(),
                'NAV_PARAMS' => [
                    'nPageSize' => $this->pagination->getPerPage(),
                    'iNumPage' => $this->pagination->getCurrentPage(),
                    'checkOutOfRange' => true
                ]
            ]
        );

        while ($element = $res->Fetch()) {
            $list[] = $this->getNewEntity()->mapData($element);
        }

        $pagination = new Pagination($this->pagination->getCurrentPage(), $this->pagination->getPerPage(), ceil($res->SelectedRowsCount() / $this->pagination->getPerPage()), $res->SelectedRowsCount());

        $result = new ListResult();
        $result->setList($list);
        $result->setPagination($pagination);

        return $result;
    }

    public function getOneResult(): ?BaseModel
    {
        $this->pagination->setPerPage(1);
        $listResult = $this->getResult();

        $list = $listResult->getList();

        if (count($list)) {
            return reset($list);
        }

        return null;
    }

    public function getCountResult(): int
    {
        $this->pagination->setPerPage(1);
        $listResult = $this->getResult();
        $pagination = $listResult->getPagination();

        return $pagination->getTotalItems();
    }
}
