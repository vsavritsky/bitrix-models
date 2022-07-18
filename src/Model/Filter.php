<?php

namespace BitrixModels\Model;

class Filter extends \BitrixFilterBuilder\Filter
{
    public static function create(): Filter
    {
        return new Filter();
    }
}
