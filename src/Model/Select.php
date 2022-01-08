<?php

namespace BitrixModels\Model;

class Select implements \JsonSerializable
{
    protected $properties = ['*'];
    protected $withProperties = false;

    public function __construct($properties = [])
    {
        $this->properties = $properties;
    }

    public function isWithProperties() : bool
    {
        return $this->withProperties;
    }

    public function withProperties() : bool
    {
        $this->withProperties = true;
        $this->properties = ['*', 'PROPERTY_*'];

        return $this->withProperties;
    }

    public function getResult() : array
    {
        return $this->properties;
    }

    public function jsonSerialize()
    {
        return $this->properties;
    }
}
