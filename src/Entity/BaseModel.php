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
    public static function iblockId()
    {
        $id = static::IBLOCK_ID;
        $code = static::IBLOCK_CODE;

        if (!$id) {
            $obCache = new CPHPCache();
            if ($obCache->InitCache(3600000000, md5($code), DIRECTORY_SEPARATOR . SITE_ID . DIRECTORY_SEPARATOR)) {
                $vars = $obCache->GetVars();
                $result = $vars['result'];
            } elseif ($obCache->StartDataCache()) {
                $arIblock = \Bitrix\Iblock\IblockTable::getList(array(
                    'filter' => array('CODE' => $code),
                ))->fetch();
                $result = $arIblock;
                $obCache->EndDataCache(['result' => $result]);
            }

            $id = $result['ID'];
        }

        if (!$id) {
            throw new LogicException(sprintf('You must set IBLOCK_ID OR IBLOCK_CODE constant inside a model or override iblockId() method or clear cache (%s, %s)', static::IBLOCK_ID, static::IBLOCK_CODE));
        }

        return $id;
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
