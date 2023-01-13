<?php

namespace BitrixModels\Repository;

use BitrixModels\QueryBuilder\ProductQueryBuilder;

class ProductRepository extends ElementRepository
{
    public function getQueryBuilder(): ProductQueryBuilder
    {
        return new ProductQueryBuilder(self::getClassModel());
    }
}
