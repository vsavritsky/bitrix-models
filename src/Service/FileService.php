<?php

namespace BitrixModels\Service;

use Bitrix\Main\IO\Path;
use Bitrix\Main\Text\UtfSafeString;
use BitrixModels\Model\FileInfo;
use CFile;

class FileService
{
    public static function create(): FileService
    {
        return new FileService();
    }

    /** @deprecated */
    public static function getLink($fileId): ?string
    {
        return self::create()->get($fileId)->getLink();
    }

    /** @deprecated */
    public static function getExtension($fileId): ?string
    {
        return self::create()->get($fileId)->getExtension();
    }

    /** @deprecated */
    public static function getFormatSize($fileId): ?string
    {
        return self::create()->get($fileId)->getFormatSize();
    }

    /** @deprecated */
    public static function getSize($fileId): ?int
    {
        return self::create()->get($fileId)->getSize();
    }

    /** @deprecated */
    public static function getOriginalName($fileId): ?string
    {
        return self::create()->get($fileId)->getOriginalName();
    }

    public function get($fileId): FileInfo
    {
        return $this->getFileInfo($fileId);
    }

    protected function getFileInfo($fileId): FileInfo
    {
        $fileInfo = new FileInfo();

        if (!$fileId) {
            return $fileInfo;
        }

        $arFile = CFile::GetById($fileId);
        $arFile = $arFile->getNext();

        if (!$arFile) {
            return $fileInfo;
        }

        $pos = UtfSafeString::getLastPosition($arFile["ORIGINAL_NAME"], '.');
        if ($pos !== false) {
            $fileInfo->setOriginalName(mb_substr($arFile["ORIGINAL_NAME"], 0, $pos));
        }

        $link = CFile::GetPath($fileId);
        $link = UrlService::getFullUrl($link);

        $fileInfo->setLink($link);
        $fileInfo->setExtension(Path::getExtension($link));
        $fileInfo->setFormatSize(CFile::FormatSize((int)$arFile["FILE_SIZE"]));
        $fileInfo->setSize((int)$arFile["FILE_SIZE"]);

        return $fileInfo;
    }
}
