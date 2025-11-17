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

            $currentPage = $this->pagination->getCurrentPage();
            $perPage = $this->pagination->getPerPage();
            $offset = ($currentPage - 1) * $perPage; // Вычисляем offset на основе страницы

            $res = CIBlockElement::GetList(
                $this->sort->getResult(),
                $this->getResultFilter($this->filter)->getResult(),
                false,
                [
                    "nTopCount" => $perPage,
                    "nOffset" => $offset,
                    'checkOutOfRange' => true
                ],
                $this->select->getResult()
            );

            $totalCount = $res->SelectedRowsCount(); // Общее количество без учета лимита

            // Собираем все элементы и их ID
            $elements = [];
            $elementIds = [];

            if ($this->select->isWithProperties()) {
                while ($ob = $res->GetNextElement()) {
                    $element = $ob->GetFields();
                    $element['PROPERTIES'] = $ob->GetProperties();

                    if ($this->select->isWithSeo()) {
                        $element['SEO'] = $this->getElementSeoConfig($element['ID']);
                    }

                    $elements[$element['ID']] = $element;
                    $elementIds[] = $element['ID'];
                }
            } else {
                while ($element = $res->GetNext()) {
                    if ($this->select->isWithSeo()) {
                        $element['SEO'] = $this->getElementSeoConfig($element['ID']);
                    }

                    $elements[$element['ID']] = $element;
                    $elementIds[] = $element['ID'];
                }
            }

            // Получаем данные каталога для всех элементов сразу
            $catalogData = $this->getCatalogDataBatch($elementIds);

            // Получаем цены для всех элементов сразу
            $pricesData = $this->getPricesDataBatch($elementIds);

            // Объединяем данные
            foreach ($elements as $id => $element) {
                if (isset($catalogData[$id])) {
                    $element = array_merge($element, $catalogData[$id]);
                }

                if (isset($pricesData[$id])) {
                    $element['PRICE'] = $this->getOptimalPrice($pricesData[$id]);
                    $element['DISCOUNT'] = $this->getDiscount($pricesData[$id]);
                } else {
                    $element['PRICE'] = 0;
                    $element['DISCOUNT'] = 0;
                }

                $list[] = $this->getNewEntity()->mapData($element);
            }

            $pagination = new Pagination(
                $currentPage,
                $perPage,
                ceil($totalCount / $perPage),
                $totalCount
            );

            $result = new ListResult();
            $result->setList($list);
            $result->setPagination($pagination);

            $cache->endDataCache(['result' => $result]);
        }

        return $result;
    }

    protected function getCatalogDataBatch(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $catalogData = [];
        $res = CCatalogProduct::GetList(
            [],
            ['ID' => $ids],
            false,
            false,
            ['ID', 'QUANTITY', 'WEIGHT', 'WIDTH', 'LENGTH', 'HEIGHT', 'MEASURE', 'VAT_ID', 'VAT_INCLUDED', 'CAN_BUY_ZERO', 'NEGATIVE_AMOUNT_TRACE', 'SUBSCRIBE']
        );

        while ($item = $res->Fetch()) {
            $catalogData[$item['ID']] = $item;
        }

        return $catalogData;
    }

    protected function getPricesDataBatch(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $pricesData = [];
        foreach ($ids as $id) {
            $arPrice = CCatalogProduct::GetOptimalPrice($id, 1, [], 'N', null, SITE_ID, []);
            if (isset($arPrice['RESULT_PRICE'])) {
                $pricesData[$id] = (array)$arPrice['RESULT_PRICE'];
            }
        }

        return $pricesData;
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
