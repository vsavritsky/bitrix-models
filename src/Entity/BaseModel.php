<?php

namespace BitrixModels\Entity;

use LogicException;

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

    public function mapData($data) : self
    {

        return $this;
    }

    public function toArray() :array
    {

        return [];
    }
}
