<?php

namespace BitrixModels\Service;

class UrlService
{
    public static function getFullUrl(string $link): string
    {
        if (strpos($link, 'http') === false) {
            $protocol = $_SERVER['PROTOCOL'] = (!empty($_SERVER['HTTPS']) || $_SERVER["SERVER_PORT"] == 443) ? 'https' : 'http';
            $link = $protocol . '://' . SITE_SERVER_NAME . $link;
        }

        return $link;
    }
}
