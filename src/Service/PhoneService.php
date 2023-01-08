<?php

namespace BitrixModels\Service;

class PhoneService
{
    public static function clear($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($phone[0] == 8) {
            $phone[0] = 7;
        }

        return $phone;
    }

    public static function format($phone)
    {
        $phone = self::clear($phone);
        $phone = '+' . $phone;

        return $phone;
    }
}
