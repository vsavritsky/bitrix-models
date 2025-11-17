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
use CIBlockSection;

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

    public function save(BaseModel &$model): ?BaseModel
    {
        return null;
    }

    public function add(array $data = [], array $properties = []): int|false
    {
        $section = new CIBlockSection;

        if ($this->getClassModel()::IBLOCK_ID) {
            $data['IBLOCK_ID'] = $this->getClassModel()::IBLOCK_ID;
        } else {
            $res = CIBlock::GetList([], ['=CODE' => $this->getClassModel()::IBLOCK_CODE], false);
            if ($arrc = $res->Fetch()) {
                $data['IBLOCK_ID'] = $arrc['ID'];
            }
        }

        if ($data) {
            $sectionId = $section->Add($data);
            
            if ($sectionId && !empty($properties)) {
                CIBlockSection::SetPropertyValues($sectionId, $data['IBLOCK_ID'], $properties);
            }
            
            return $sectionId;
        }

        return false;
    }

    public function update(int $id, array $data = [], array $properties = []): bool
    {
        $section = new CIBlockSection;
        $result = true;

        if (!empty($data)) {
            $result = $section->Update($id, $data);
        }

        if ($result && !empty($properties)) {
            $iblockId = $data['IBLOCK_ID'] ?? $this->getClassModel()::iblockId();
            CIBlockSection::SetPropertyValuesEx($id, $iblockId, $properties);
        }

        return $result;
    }
}
