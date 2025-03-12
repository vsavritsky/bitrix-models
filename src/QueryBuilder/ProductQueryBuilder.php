<?php

namespace BitrixModels\QueryBuilder;

use Bitrix\Main\Data\Cache;
use BitrixModels\Model\ListResult;
use BitrixModels\Model\Pagination;
use CIBlock;
use CIBlockElement;
use CCatalogProduct;

class ProductQueryBuilder extends ElementQueryBuilder
{
    /** @var string */
    protected $class;

    protected $fields = [];
    protected $properties = [];

    public function __construct($class)
    {
        parent::__construct($class);

        $this->class = $class;
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
                ["nPageSize" => $this->pagination->getPerPage(), 'iNumPage' => $this->pagination->getCurrentPage(), 'checkOutOfRange' => true],
                $this->select->getResult()
            );

            if ($this->select->isWithProperties()) {
                while ($ob = $res->GetNextElement()) {
                    $element = $ob->GetFields();
                    $element['PROPERTIES'] = $ob->GetProperties();

                    if ($this->select->isWithSeo()) {
                        $element['SEO'] = $this->getElementSeoConfig($element['ID']);
                    }

                    $arCatalogData = CCatalogProduct::GetByID($element['ID']);
                    $element = array_merge($element, $arCatalogData);

                    $arPrice = $this->getPriceData($element['ID']);

                    $element['PRICE'] = $this->getOptimalPrice($arPrice);
                    $element['DISCOUNT'] = $this->getDiscount($arPrice);

                    $list[] = $this->getNewEntity()->mapData($element);
                }
            } else {
                while ($element = $res->GetNext()) {
                    if ($this->select->isWithSeo()) {
                        $element['SEO'] = $this->getElementSeoConfig($element['ID']);
                    }

                    $arPrice = $this->getPriceData($element['ID']);
                    $element['PRICE'] = $this->getOptimalPrice($arPrice);
                    $element['DISCOUNT'] = $this->getDiscount($arPrice);

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

    protected function getPriceData(int $id, int $count = 1): array
    {
        $arPrice = CCatalogProduct::GetOptimalPrice($id, $count, [], 'N', null, SITE_ID, []);
        return (array)$arPrice['RESULT_PRICE'];
    }

    protected function getOptimalPrice(array $arPrice): float
    {
        return (float)$arPrice['DISCOUNT_PRICE'];
    }

    protected function getDiscount(array $arPrice): float
    {
        if (!$arPrice['BASE_PRICE']) {
            return 0;
        }

        $discount = 100 - ($arPrice['DISCOUNT_PRICE'] / $arPrice['BASE_PRICE']) * 100;
        $discount = round($discount, 2);

        return (float)$discount;
    }
}
