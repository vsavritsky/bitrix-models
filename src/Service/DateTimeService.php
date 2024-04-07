<?php

namespace BitrixModels\Service;

use DateTime;

class DateTimeService
{
    public static function format($dateString): string
    {
        $date = null;

        if (is_a($dateString, DateTime::class)) {
            $date = $dateString;
        } elseif (is_string($dateString)) {
            $date = DateTime::createFromFormat('d.m.Y H:i:s', $dateString);

            if (!$date) {
                $date = DateTime::createFromFormat('d.m.Y', $dateString);
                $date->setTime(0, 0, 0);
            }
        }

        if (!$date) {
            return '';
        }

        return $date->format(DATE_ATOM);
    }
}
