<?php

namespace BitrixModels\Service;

class UrlService
{
    public static function getFullUrl(string $link): string
    {
        $domain = SITE_SERVER_NAME;
        if (!$domain) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        if (!str_starts_with($link, 'http')) {
            $protocol = $_SERVER['PROTOCOL'] = (!empty($_SERVER['HTTPS']) || $_SERVER["SERVER_PORT"] == 443) ? 'https' : 'http';
            $link = $protocol . '://' . $domain . $link;
        }

        return $link;
    }
}