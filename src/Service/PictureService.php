<?php

namespace BitrixModels\Service;

use CFile;

class PictureService
{
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_BIG = 'big';
    const SIZE_REFERENCE = 'reference';

    const ENTITY_DEFAULT = 'default';

    public static function getDefaultSizes()
    {
        return [self::SIZE_SMALL, self::SIZE_MEDIUM, self::SIZE_BIG, self::SIZE_REFERENCE];
    }

    protected static function getSizeConfig($size, $entity)
    {
        $config = ['width' => 1000, 'height' => 1000];

        switch ($entity) {
            case self::ENTITY_DEFAULT:
                switch ($size) {
                    case self::SIZE_SMALL:
                        $config = ['width' => 380, 'height' => 300];
                        break;
                    case self::SIZE_MEDIUM:
                        $config = ['width' => 860, 'height' => 860];
                        break;
                    case self::SIZE_BIG:
                        $config = ['width' => 1400, 'height' => 1400];
                        break;
                    case self::SIZE_REFERENCE:
                        $config = ['width' => 100000, 'height' => 100000];
                        break;
                }
                break;
        }

        return $config;
    }

    public static function getPicture($imgId, $size = self::SIZE_SMALL, $entity = self::ENTITY_DEFAULT): ?string
    {
        if (!$imgId) {
            return null;
        }

        $compression = 80;

        if ($size == self::SIZE_REFERENCE) {
            $compression = 0;
        }

        $file = CFile::ResizeImageGet($imgId, self::getSizeConfig($size, $entity), BX_RESIZE_IMAGE_PROPORTIONAL, true, false, false, $compression);
        $link = $file['src'];

        if (strpos($link, 'http') === false) {
            $link = EnvService::parameter('DOMAIN') . $link;
        }

        if (strpos($link, 'https://') === false) {
            $link = str_replace('http:', 'https:', $link);
        }

        return $link;
    }

    public static function getFileSize($imgId): ?int
    {
        if (!$imgId) {
            return null;
        }

        $fileData = CFile::GetFileArray($imgId);

        return (int)$fileData['FILE_SIZE'];
    }
}
