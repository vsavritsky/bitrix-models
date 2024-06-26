<?php

namespace BitrixModels\Service;

class PhoneService
{
    /** @deprecated */
    protected static function clear($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if ($phone[0] == 8) {
            $phone[0] = 7;
        }

        return $phone;
    }

    public static function format($phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if ($phone[0] == 8) {
            $phone[0] = 7;
        }

        return '+' . $phone;
    }
}
