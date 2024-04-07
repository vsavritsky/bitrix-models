<?php

namespace BitrixModels\Service;

use DateTime;

class DateTimeService
{
    const DEFAULT_DATETIME_FORMAT = 'd.m.Y H:i:s';
    const DEFAULT_DATE_FORMAT = 'd.m.Y';

    public static function create(): DateTimeService
    {
        return new DateTimeService();
    }

    public static function format($dateString): string
    {
        $date = null;

        if (is_a($dateString, DateTime::class)) {
            $date = $dateString;
        } elseif ($dateString && is_string($dateString)) {
            $date = DateTime::createFromFormat(self::DEFAULT_DATETIME_FORMAT, $dateString);

            if (!$date) {
                $date = DateTime::createFromFormat(self::DEFAULT_DATE_FORMAT, $dateString);
                if ($date) {
                    $date->setTime(0, 0, 0);
                }
            }
        }

        if (!$date) {
            return '';
        }

        return $date->format(DATE_ATOM);
    }
}
