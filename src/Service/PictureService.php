<?php

namespace BitrixModels\Service;

use CFile;

class PictureService
{
    const SIZE_SMALL = 'small';
    const SIZE_MEDIUM = 'medium';
    const SIZE_BIG = 'big';
    const SIZE_REFERENCE = 'reference';

    protected static function getSizeConfig($size): array
    {
        switch ($size) {
            case self::SIZE_SMALL:
                $config = ['width' => 380, 'height' => 300];
                break;
            case self::SIZE_MEDIUM:
                $config = ['width' => 860, 'height' => 860];
                break;
            case self::SIZE_BIG:
                $config = ['width' => 1920, 'height' => 1920];
                break;
            case self::SIZE_REFERENCE:
                $config = ['width' => 100000, 'height' => 100000];
                break;
            default:
                $config = ['width' => 1000, 'height' => 1000];
        }

        return $config;
    }

    public static function getPicture($imgId, $size = self::SIZE_SMALL, bool $fullPath = false): ?string
    {
        $compression = 80;
        if ($size == self::SIZE_REFERENCE) {
            $compression = 0;
        }

        $size = self::getSizeConfig($size);

        return self::getPictureWithCustomSize($imgId, $size['width'], $size['height'], $compression, $fullPath);
    }

    public static function getPictureWithCustomSize($imgId, int $width = 300, int $height = 300, int $compression = 80, bool $fullPath = false): ?string
    {
        if (!$imgId) {
            return null;
        }

        $file = CFile::ResizeImageGet($imgId, ['width' => $width, 'height' => $height], BX_RESIZE_IMAGE_PROPORTIONAL, true, false, false, $compression);
        $link = $file['src'];

        if (strpos($link, 'http') === false && $fullPath) {
            $protocol = $_SERVER['PROTOCOL'] = (!empty($_SERVER['HTTPS']) || $_SERVER["SERVER_PORT"] == 443) ? 'https' : 'http';
            $link = $protocol . '://' . SITE_SERVER_NAME . $link;
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
