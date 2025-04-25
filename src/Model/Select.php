<?php

namespace BitrixModels\Model;

class Select implements \JsonSerializable
{
    protected array $properties = ['*'];
    protected bool $withProperties = false;
    protected bool $withSeo = false;

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

    public function isWithSeo(): bool
    {
        return $this->withSeo;
    }

    public function addField(string $code): self
    {
        $this->properties[] = $code;

        return $this;
    }

    public function addProperty(string $code): self
    {
        $this->withProperties();
        $this->properties[] = 'PROPERTY_' . $code;

        return $this;
    }

    public function withProperties(bool $value = true): self
    {
        $this->withProperties = $value;
        return $this;
    }

    public function withAllProperties(): self
    {
        $this->withProperties();
        $this->properties = ['*', 'PROPERTY_*', 'UF_*'];

        return $this;
    }

    public function withSeo(): self
    {
        $this->withSeo = true;

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
