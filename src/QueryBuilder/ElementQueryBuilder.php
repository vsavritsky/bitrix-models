<?php

namespace BitrixModels\QueryBuilder;

use Bitrix\Main\Data\Cache;
use BitrixModels\Entity\BaseModel;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use CIBlock;
use CIBlockElement;

class ElementQueryBuilder extends BaseQueryBuilder
{
    /** @var string */
    protected $class;

    protected $fields = [];
    protected $properties = [];

    public function __construct($class)
    {
        parent::__construct();

        $this->class = $class;

        //$this->loadFields();
        //$this->loadProperties();
    }

    //protected function loadFields()
    //{
    //    $this->fields = CIBlock::GetFields(self::getClassModel()::iblockId());
    //}

    //protected function loadProperties()
    //{
    //    $res = CIBlock::GetProperties(self::getClassModel()::iblockId(), [], []);
    //    if ($arRes = $res->Fetch()) {
    //        $this->properties[] = $arRes;
    //    }
    //}
    
    protected function getResultFilter(Filter $filter = null): Filter
    {
        $filter->eq('IBLOCK_ID', $this->class::iblockId());
		
        return $filter;
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
            $res = CIBlockElement::GetList(
                $this->sort->getResult(),
                $this->getResultFilter($this->filter)->getResult(),
                false,
                ["nPageSize" => $this->pagination->getPerPage(), 'iNumPage' => $this->pagination->getCurrentPage()],
                $this->select->getResult()
            );

            if ($this->select->isWithProperties()) {
                while ($ob = $res->GetNextElement()) {
                    $element = $ob->GetFields();
                    $element['PROPERTIES'] = $ob->GetProperties();

                    $list[] = $this->getNewEntity()->mapData($element);
                }
            } else {
                while ($element = $res->GetNext()) {
                    $list[] = $this->getNewEntity()->mapData($element);
                }
            }

            $pagination = new Pagination($this->pagination->getCurrentPage(), $this->pagination->getPerPage(), ceil($res->SelectedRowsCount() / $this->pagination->getPerPage()), $res->SelectedRowsCount());

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
        $this->pagination->setPerPage(1);
        $listResult = $this->getResult();
        $pagination = $listResult->getPagination();

        return $pagination->getTotalItems();
    }
}
