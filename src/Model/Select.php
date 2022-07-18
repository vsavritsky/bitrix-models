<?php

namespace BitrixModels\Model;

class Select implements \JsonSerializable
{
    protected $properties = ['*'];
    protected bool $withProperties = false;

    public function __construct($properties = [])
    {
        $this->properties = $properties;
    }

    public static function create(): Select
    {
        return new Select();
    }

    public function isWithProperties(): bool
    {
        return $this->withProperties;
    }

    public function addField(string $code): self
    {
        $this->properties[] = $code;

        return $this;
    }

    public function addProperty(string $code): self
    {
        $this->properties[] = 'PROPERTY_' . $code;

        return $this;
    }

    public function withProperties(): self
    {
        $this->withProperties = true;
        $this->properties = ['*', 'PROPERTY_*', 'UF_*'];

        return $this;
    }

    public function getResult(): array
    {
        return $this->properties;
    }

    public function jsonSerialize()
    {
        return $this->properties;
    }
}
