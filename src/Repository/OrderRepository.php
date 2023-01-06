<?php

namespace BitrixModels\Repository;

use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\Filter;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;
use BitrixModels\Repository\BaseRepository;
use Bitrix\Sale\Order as BOrder;
use Bitrix\Sale;
use CSaleOrder;

class OrderRepository extends BaseRepository
{
    protected $class = BOrder::class;

    public function findById($id): ?BaseModel
    {
        $order = BOrder::load($id);
        return $order;
    }

    public function findByExtId($extId): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('XML_ID', $extId);

        return null;
    }

    public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel
    {
        $result = $this->findByFilter(new Select(), $filter, $sort, 1, 1);
        return $result->getList()[0];
    }

    public function countByFilter(Filter $filter = null): int
    {
        //['USER_ID' => $user['ID'], 'STATUS_ID' => ['N', 'W']]
        return Sale\Internals\OrderTable::getCount([
            $filter->getResult()
        ]);
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

        $list = [];
        $rsSales = CSaleOrder::GetList($sort->getResult(), $filter->getResult(), false, ['iNumPage' => $page, 'nPageSize' => $count]);
        while ($arSale = $rsSales->Fetch()) {
            $list[] = Sale\Order::load($arSale['ID']);
        }

        $pagination = new Pagination();
        $pagination->setPerPage((int)$count);
        $pagination->setTotalItems(ceil($rsSales->SelectedRowsCount() / $count));
        $pagination->setCountElements((int)$rsSales->SelectedRowsCount());
        $pagination->setCurrentPage((int)$page);

        $listResult = new ListResult();
        $listResult->setList($list);
        $listResult->setPagination($pagination);

        return $listResult;
    }
}
