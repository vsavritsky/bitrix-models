<?php

namespace BitrixModels\Entity;

use Bitrix\Iblock\IblockTable;
use CPHPCache;
use LogicException;

class BaseModel
{
    /**
     * @var int
     */
    const IBLOCK_ID = null;
    const IBLOCK_CODE = null;

    public function __construct($id = null)
    {
        $this->setId($id);
    }

    /**
     * Getter for corresponding iblock id.
     *
     * @return int
     * @throws LogicException
     *
     */
    public static function iblockId(): int
    {
        $id = static::IBLOCK_ID;
        $code = static::IBLOCK_CODE;

        if (!$id && !$code) {
            throw new LogicException(sprintf('You must set IBLOCK_ID OR IBLOCK_CODE constant inside a model or override iblockId() method (%s, %s)', static::IBLOCK_ID, static::IBLOCK_CODE));
        }

        if (!$id) {
            $result = null;
            $cacheId = md5(static::class . '_' . $code);
            $cacheDir = DIRECTORY_SEPARATOR . (defined('SITE_ID') ? SITE_ID : 'default') . DIRECTORY_SEPARATOR;
            
            $obCache = new CPHPCache();
            if ($obCache->InitCache(3600000000, $cacheId, $cacheDir)) {
                $vars = $obCache->GetVars();
                $result = $vars['result'] ?? null;
            } elseif ($obCache->StartDataCache()) {
                $arIblock = \Bitrix\Iblock\IblockTable::getList([
                    'filter' => ['CODE' => $code],
                ])->fetch();
                
                $result = $arIblock ?: null;
                $obCache->EndDataCache(['result' => $result]);
            }

            if ($result && isset($result['ID'])) {
                $id = (int)$result['ID'];
            }
        }

        if (!$id) {
            throw new LogicException(sprintf('Iblock not found by CODE "%s". You must set IBLOCK_ID OR IBLOCK_CODE constant inside a model or override iblockId() method or clear cache (%s, %s)', $code, static::IBLOCK_ID, static::IBLOCK_CODE));
        }

        return (int)$id;
    }

    public static function camel(string $name): string
    {
        $name = mb_strtolower($name);
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }

    public static function snake(string $name): string
    {
        $name = mb_strtolower($name);
        return mb_strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
    }

    public function toCamelCase(string $name): string
    {
        return self::camel($name);
    }

    public function toSnakeCase(string $name): string
    {
        return self::snake($name);
    }

    public function getField($name)
    {
        return null;
    }

    public function mapData($data): self
    {
        return $this;
    }

    public function toArray(): array
    {
        return [];
    }
}
