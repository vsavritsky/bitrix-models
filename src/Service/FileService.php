<?php

namespace BitrixModels\Service;

use Bitrix\Main\IO\Path;
use Bitrix\Main\Text\UtfSafeString;
use CFile;

class FileService
{
    public static function getLink($fileId)
    {
        if (!$fileId) {
            return null;
        }

        $link = CFile::GetPath($fileId);

        if (strpos($link, 'http') === false) {
            $link = EnvService::parameter('DOMAIN') . $link;
        }

        return $link;
    }

    public static function getExtension($fileId)
    {
        if (!$fileId) {
            return null;
        }

        $link = CFile::GetPath($fileId);

        return Path::getExtension($link);
    }

    public static function getFormatSize($fileId)
    {
        $size = self::getSize($fileId);
        if (!$size) {
            return null;
        }

        return CFile::FormatSize($size);
    }

    public static function getSize($fileId)
    {
        if (!$fileId) {
            return null;
        }

        $arFile = CFile::GetById($fileId);
        $arFile = $arFile->getNext();
        return (int)$arFile["FILE_SIZE"];
    }

    public static function getOriginalName($fileId)
    {
        if (!$fileId) {
            return null;
        }

        $arFile = CFile::GetById($fileId);
        $arFile = $arFile->getNext();
        $pos = UtfSafeString::getLastPosition($arFile["ORIGINAL_NAME"], '.');
        if ($pos !== false)
            return mb_substr($arFile["ORIGINAL_NAME"], 0, $pos);
        return '';
    }
}
