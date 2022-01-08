<?php

namespace BitrixModels\Entity;

class Property
{
    protected $name;
    protected $code;
    protected $xmlId;
    protected $enumId;
    protected $value;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getXmlId()
    {
        return $this->xmlId;
    }

    /**
     * @param mixed $xmlId
     */
    public function setXmlId($xmlId): void
    {
        $this->xmlId = $xmlId;
    }

    /**
     * @return mixed
     */
    public function getEnumId()
    {
        return $this->enumId;
    }

    /**
     * @param mixed $enumId
     */
    public function setEnumId($enumId): void
    {
        $this->enumId = $enumId;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}