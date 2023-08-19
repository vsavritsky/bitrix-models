<?php

namespace BitrixModels\Entity;

use Bitrix\Main\Type;
use DateTimeInterface;

class ProductModel extends ElementModel
{
    public function getPrice(): float
    {
        return (float)$this->getField('PRICE') ? $this->getField('PRICE')->getValue() : 0;
    }

    public function getDiscount(): float
    {
        return (float)$this->getField('DISCOUNT') ? $this->getField('DISCOUNT')->getValue() : 0;
    }

    public function getCatalogQuantity(): float
    {
        return (float)$this->getField('CATALOG_QUANTITY');
    }

    public function getCatalogWeight(): float
    {
        return (float)$this->getField('CATALOG_WEIGHT');
    }
}
