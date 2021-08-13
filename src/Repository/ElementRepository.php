<?php

namespace BitrixModels\Repository;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\Iblock;
use BitrixModels\Entity\BaseModel;
use BitrixFilterBuilder\Filter;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use BitrixModels\Model\Sort;
use Cache\CacheManager;
use CIBlockElement;

class ElementRepository extends BaseRepository
{
    public function countByFilter(Filter $filter = null): int
    {
        $filter = $this->getResultFilter($filter);

        $dbItem = ElementTable::getList([
            'select' => ['ID'],
            'filter' => $filter->getResult(),
            'count_total' => 1, // дает возможность получить кол-во элементов через метод getCount()
        ]);

        return (int)$dbItem->getCount();
    }

    protected function getResultFilter(Filter $filter = null): Filter
    {
        $filter = parent::getResultFilter($filter);
        $filter->eq('IBLOCK_ID', self::getClassModel()::iblockId());

        return $filter;
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
        ];

        if ($sort) {
            $params['order'] = $sort->getResult();
        }

        $dbItem = ElementTable::getList($params);

        if ($arItem = $dbItem->fetch()) {
            $arItem['PROPERTIES'] = [];
            $elements[$arItem['ID']] = $arItem;
            CIBlockElement::GetPropertyValuesArray($elements, self::getClassModel()::iblockId(), $filter->getResult());
        }

        return $this->getNewEntity()->mapData($elements[$arItem['ID']]);
    }

    public function findByFilter(Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult
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

        $dbItems = ElementTable::getList($params);
        while ($arItem = $dbItems->fetch()) {
            $arItem['PROPERTIES'] = [];
            $elements[$arItem['ID']] = $arItem;
        }

        CIBlockElement::GetPropertyValuesArray($elements, self::getClassModel()::iblockId(), $filter->getResult());

        $list = [];
        foreach ($elements as $element) {
            $list[] = $this->getNewEntity()->mapData($element);
        }

        $pagination = new Pagination($page, $count, ceil($dbItems->getSelectedRowsCount() / $count), $dbItems->getSelectedRowsCount());

        $result = new ListResult();
        $result->setList($list);
        $result->setPagination($pagination);

        return $result;
    }

    public function findFieldByFilter($fields, Filter $filter = null, Sort $sort = null, int $count = 10, int $page = 1): ListResult
    {
        // TODO: Implement findFieldByFilter() method.
    }

    public function save(BaseModel &$model): ?BaseModel
    {
        parent::save($model);

        $el = new CIBlockElement;

        if (!$model->getId()) {
            $fields = $model->getFields();
            $fields['PROPERTY_VALUES'] = $model->getProperties();

            if ($productId = $el->Add($fields)) {
                $model->setId($productId);

                $model = $this->findById($model->getId());

                return $model;
            }
            echo "Error: " . $el->LAST_ERROR;
        } else {
            $fields = $model->getUpdatedFields();
            $properties = $model->getUpdatedProperties();

            if ($fields) {
                $el->Update($model->getId(), $fields);
            }

            foreach ($properties as $propertyKey => $propertyValue) {
                CIBlockElement::SetPropertyValuesEx($model->getId(), $model::iblockId(), [$propertyKey => $propertyValue]);
            }

            $model = $this->findById($model->getId());
            return $model;
        }

        return null;
    }

    public function findById($id): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('ID', $id);

        return $this->findOneByFilter($filter);
    }

    public function update($id, $fields = [], $properties = [])
    {
        $el = new \CIBlockElement;

        if ($fields) {
            $res = $el->Update($id, $fields);
        }

        foreach ($properties as $propertyKey => $propertyValue) {
            \CIBlockElement::SetPropertyValuesEx($id, null, [$propertyKey => $propertyValue]);
        }

        //CacheManager::clearByTag($id);

        return $res;
    }
}