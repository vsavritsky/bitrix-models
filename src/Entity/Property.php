<?php

namespace BitrixModels\Entity;

class Property
{
    protected string $name = '';
    protected string $code = '';
    protected $xmlId = '';
    protected $enumId = '';
    protected $value;
    protected $description = '';
    protected string $type = '';
    protected bool $multiple = false;

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

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = mb_strtolower($type);
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public function setMultiple(bool $multiple): void
    {
        $this->multiple = $multiple;
    }

    public function toArray(string $valueCode, string $descriptionCode): array
    {
        $list = [];
        foreach ($this->getValue() as $key => $name) {
            $code = $this->getDescription()[$key];
            $list[] = [
                $valueCode => $name,
                $descriptionCode => $code,
            ];
        }

        return $list;
    }
}
