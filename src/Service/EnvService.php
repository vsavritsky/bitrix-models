<?php

namespace BitrixModels\Service;

use Bitrix\Main\Config\Option;

class EnvService
{
    public function getParameter(string $code, string $module = 'main')
    {
        return self::parameter($code, $module);
    }

    public static function parameter(string $code, string $module = 'main')
    {
        $adminValue = Option::get($module, $code);

        if ($adminValue) {
            return $adminValue;
        }

        return $_ENV[$code];
    }
}
