<?php

namespace BitrixModels\Repository;

use BitrixModels\Model\Filter;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\Iblock;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Select;
use BitrixModels\Model\Sort;
use BitrixModels\QueryBuilder\ElementQueryBuilder;
use Cache\CacheManager;
use CIBlock;
use CIBlockElement;

class ElementRepository extends BaseRepository
{
    public function getQueryBuilder(): ElementQueryBuilder
    {
        return new ElementQueryBuilder(self::getClassModel());
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

        return $this->getQueryBuilder()->filter($filter)->select($select)->getOneResult();
    }

    public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel
    {
        return $this->getQueryBuilder()->filter($filter)->sort($sort)->getOneResult();
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

    public function add($fields = [], $properties = [])
    {
        $el = new CIBlockElement;

        if ($this->getClassModel()::IBLOCK_ID) {
            $fields['IBLOCK_ID'] = $this->getClassModel()::IBLOCK_ID;
        } else {
            $res = CIBlock::GetList([], ['=CODE' => $this->getClassModel()::IBLOCK_CODE], false);
            if ($arrc = $res->Fetch()) {
                $fields['IBLOCK_ID'] = $arrc['ID'];
            }
        }

        $fields['PROPERTY_VALUES'] = $properties;

        if ($fields) {
            $res = $el->Add($fields);
        }

        return $res;
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
