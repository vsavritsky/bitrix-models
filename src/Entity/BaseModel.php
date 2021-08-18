<?php

namespace BitrixModels\Entity;

use LogicException;
use CPHPCache;
use \Bitrix\Iblock\IblockTable;

class BaseModel
{
    /**
     * @var int
     */
    const IBLOCK_ID = null;

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
            $cacheTime = 360000;
            $cacheId = md5($code);
            $cachePath = DIRECTORY_SEPARATOR . SITE_ID . DIRECTORY_SEPARATOR;
            if ($obCache->InitCache($cacheTime, $cacheId, $cachePath)) {
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
            throw new LogicException('You must set IBLOCK_ID constant inside a model or override iblockId() method');
        }

        return $id;
    }

    protected function toCamelCase($name)
    {
        $name = mb_strtolower($name);
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $name))));
    }

    protected function toSnakeCase($name)
    {
        $name = mb_strtolower($name);
        $name = mb_strtoupper(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
        return $name;
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
