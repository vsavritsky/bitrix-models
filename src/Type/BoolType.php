<?php

namespace BitrixModels\Type;

use CIBlockElement;

class BoolType
{
    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'N',
            'USER_TYPE' => 'bool',
            'DESCRIPTION' => 'Да / Нет',
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB']
        );
    }

    //формируем пару полей для создаваемого асвойства
    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $html = '<input type="checkbox" name="' . $strHTMLControlName["VALUE"] . '" value="1" ' . ($value['VALUE'] ? 'checked="checked"' : '') . '>';
        return $html;
    }

    //сохраняем в базу
    public static function ConvertToDB($arProperty, $value)
    {
        $value['VALUE'] = (int)boolval($value['VALUE']);
        return $value;
    }

    //читаем из базы
    public static function ConvertFromDB($arProperty, $value)
    {
        $value['VALUE'] = (int)boolval($value['VALUE']);
        return $value;
    }
}
