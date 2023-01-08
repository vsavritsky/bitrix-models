<?php

namespace BitrixModels\Service;

class GeoService
{
    public static function getCityById($id)
    {
        $res = \Bitrix\Sale\Location\LocationTable::getList([
            'filter' => [
                'ID' => $id,
                //'=TYPE_CODE' => 'CITY',
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ],
            'select' => [
                'ID',
                '*',
                'NAME_RU' => 'NAME.NAME',
                'PARENT_ID' => 'PARENT_ID',
                'TYPE_CODE' => 'TYPE.CODE',
            ]
        ]);
        if ($location = $res->fetch()) {
            return [
                'id' => $location['ID'],
                'code' => $location['CODE'],
                'name' => $location['NAME_RU'],
            ];
        }

        return null;
    }

    public static function getCityByName($name)
    {
        $res = \Bitrix\Sale\Location\LocationTable::getList([
            'filter' => [
                '=NAME.NAME' => $name,
                //'=TYPE_CODE' => 'CITY',
                '=NAME.LANGUAGE_ID' => LANGUAGE_ID,
                '=TYPE.NAME.LANGUAGE_ID' => LANGUAGE_ID,
            ],
            'select' => [
                'ID',
                '*',
                'NAME_RU' => 'NAME.NAME',
                'PARENT_ID' => 'PARENT_ID',
                'TYPE_CODE' => 'TYPE.CODE',
            ]
        ]);
        if ($location = $res->fetch()) {
            return [
                'id' => $location['ID'],
                'code' => $location['CODE'],
                'name' => $location['NAME_RU'],
            ];
        }

        return null;
    }
}
