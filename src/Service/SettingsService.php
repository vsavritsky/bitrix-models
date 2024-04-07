<?php

namespace BitrixModels\Service;

use Bitrix\Main\Config\Option;

class SettingsService
{
    public function getValue(string $code, string $module = 'main', $defaultValue = null)
    {
        return self::get($code, $module, $defaultValue);
    }

    public static function get(string $code, string $module = 'main', $defaultValue = null)
    {
        $value = Option::get($module, $code);

        if ($value) {
            return $value;
        }

        if ($_ENV[$code]) {
            return $_ENV[$code];
        }

        return $defaultValue;
    }
}
