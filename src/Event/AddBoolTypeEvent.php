<?php

namespace BitrixModels\Event;

use Bitrix\Main\EventManager;

class AddBoolTypeEvent extends AbstractEvent
{
    public static function register(): void
    {
        EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', [\BitrixModels\Type\BoolType::class, 'GetUserTypeDescription']);
    }
}
