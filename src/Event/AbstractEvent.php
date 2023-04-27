<?php

namespace BitrixModels\Event;

use Bitrix\Main\EventManager;

abstract class AbstractEvent
{
    abstract public static function register(): void;
}
