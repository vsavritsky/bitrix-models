<?php

namespace BitrixModels\Service;
class DateTimeService
{
    public static function format($dateString): string
    {
        if ($dateString) {
            $date = \DateTime::createFromFormat('d.m.Y H:i:s', $dateString);

            if (!$date) {
                $date = \DateTime::createFromFormat('d.m.Y', $dateString);
                $date->setTime(0, 0, 0);
            }
        }

        if (!$date) {
            return '';
        }

        return $date->format(DATE_ATOM);
    }
}
