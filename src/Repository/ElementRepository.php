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

        $select = new Select();
        $select->withAllProperties();
        $select->withSeo();

        return $this->getQueryBuilder()->filter($filter)->select($select)->getOneResult();
    }

    public function findById($id): ?BaseModel
    {
        $filter = new Filter();
        $filter->eq('ID', $id);

        $select = new Select();
        $select->withAllProperties();
        $select->withSeo();

        return $this->getQueryBuilder()->filter($filter)->select($select)->getOneResult();
    }

    public function findOneByFilter(Filter $filter = null, Sort $sort = null): ?BaseModel
    {
        if (!$filter) {
            $filter = new Filter();
        }

        $select = new Select();
        $select->withAllProperties();
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
            $select->withAllProperties();
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

    public function add(array $data = [], array $properties = []): int|false
    {
        $el = new CIBlockElement;

        $data['IBLOCK_ID'] = $this->getClassModel()::iblockId();
        $data['PROPERTY_VALUES'] = $properties;

        $this->setLastError('');

        $res = $el->Add($data);
        
        if (!$res) {
            $this->setLastError($el->LAST_ERROR);
        }

        return $res;
    }

    public function update(int $id, array $data = [], array $properties = []): bool
    {
        $el = new \CIBlockElement;
        $result = false;

        $this->setLastError('');

        if ($data) {
            $result = $el->Update($id, $data);
            
            if (!$result) {
                $this->setLastError($el->LAST_ERROR);
            }
        }

        foreach ($properties as $propertyKey => $propertyValue) {
            \CIBlockElement::SetPropertyValuesEx($id, $this->getClassModel()::iblockId(), [$propertyKey => $propertyValue]);
        }

        //CacheManager::clearByTag($id);

        return $result;
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
}
