<?php

namespace BitrixModels\QueryBuilder;

use Bitrix\Main\Data\Cache;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use CIBlock;
use CIBlockElement;
use Bitrix\Highloadblock as HL;

class HighloadQueryBuilder extends BaseQueryBuilder
{
    /** @var string */
    protected $class;

    protected $fields = [];
    protected $properties = [];

    protected $entityDataClass;

    public function __construct($class)
    {
        parent::__construct();

        $this->class = $class;
        $this->hlblock = HL\HighloadBlockTable::getById($this->getClassModel()::iblockId())->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($this->hlblock);
        $this->entityDataClass = $entity->getDataClass();
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
        $cache = Cache::createInstance();
        if ($cache->initCache($this->cacheTime, $this->getCacheId())) {
            $arCacheData = $cache->GetVars();
            $result = $arCacheData['result'];
        } elseif ($cache->startDataCache()) {
            $list = [];

            $params = [
                'order' => $this->sort->getResult(),
                'select' => $this->select->getResult(),
                'filter' => $this->getResultFilter($this->filter)->getResult(),
                'limit' => $this->pagination->getPerPage(),
                'offset' => ($this->pagination->getCurrentPage() - 1) * $this->pagination->getPerPage()
            ];

            $res = $this->entityDataClass::getList($params);
            while ($element = $res->Fetch()) {
                $list[] = $this->getNewEntity()->mapData($element);
            }

            $pagination = new Pagination($this->pagination->getCurrentPage(), $this->pagination->getPerPage(), ceil($res->getSelectedRowsCount() / $this->pagination->getPerPage()), $res->getSelectedRowsCount());

            $result = new ListResult();
            $result->setList($list);
            $result->setPagination($pagination);

            $cache->endDataCache(['result' => $result]);
        }

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
        $params = [
            'filter' => $this->getResultFilter($this->filter)->getResult(),
            'offset' => 0,
            'limit' => 9999999999
        ];

        $res = $this->entityDataClass::getList($params);

        return $res->getSelectedRowsCount();
    }
}
