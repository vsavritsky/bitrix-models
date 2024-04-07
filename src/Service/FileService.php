<?php

namespace BitrixModels\Service;

use Bitrix\Main\IO\Path;
use Bitrix\Main\Text\UtfSafeString;
use BitrixModels\Model\FileInfo;
use CFile;

class FileService
{
    public static function getLink($fileId): ?string
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

    public static function getExtension($fileId): ?string
    {
        if (!$fileId) {
            return null;
        }

        $link = CFile::GetPath($fileId);

        return Path::getExtension($link);
    }

    public static function getFormatSize($fileId): ?string
    {
        $size = self::getSize($fileId);
        if (!$size) {
            return null;
        }

        return CFile::FormatSize($size);
    }

    public static function getSize($fileId): ?int
    {
        if (!$fileId) {
            return null;
        }

        $arFile = CFile::GetById($fileId);
        $arFile = $arFile->getNext();
        return (int)$arFile["FILE_SIZE"];
    }

    public static function getOriginalName($fileId): ?string
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

    public function getFileInfo($fileId): FileInfo
    {
        $fileInfo = new FileInfo();
        $fileInfo->setLink($this->getLink($fileId));
        $fileInfo->setExtension($this->getExtension($fileId));
        $fileInfo->setFormatSize($this->getFormatSize($fileId));
        $fileInfo->setOriginalName($this->getOriginalName($fileId));

        return $fileInfo;
    }
}
