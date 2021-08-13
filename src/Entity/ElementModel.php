<?php

namespace BitrixModels\Entity;

use Bitrix\Main\Type;

class ElementModel extends BaseModel
{
    protected $originData = [];

    public $fields = [
        'id' => null,
        'name' => '',
        'xmlId' => '',
        'active' => '',
        'activeFrom' => '',
        'activeTo' => '',
        'sort' => '',
        'previewPicture' => '',
        'previewText' => '',
        'detailText' => '',
        'detailPicture' => '',
        'iblockSectionId' => '',
    ];
    public $properties = [];

    protected $updatedFields = [];
    protected $updatedProperties = [];

    public function mapData($data = []): self
    {
        $this->originData = $data;

        foreach ($data['PROPERTIES'] as $key => $value) {
            $this->properties[$this->toCamelCase($key)] = [
                'name' => $value['NAME'],
                'code' => $value['CODE'], 
                'xmlid' => $value['VALUE_XML_ID'],
                'enumid' => $value['VALUE_ENUM_ID'],
                'value' => $value['VALUE']
            ];
        }
        unset($data['PROPERTIES']);

        foreach ($data as $key => $value) {
            $key = $this->toCamelCase($key);
            if (is_a($value, Type\DateTime::class)) {
                $value = $value->format(\DateTimeInterface::ATOM);
            }
            $this->fields[$key] = $value;
        }

        $this->refresh();

        return $this;
    }

    protected function refresh()
    {
        $this->updatedFields = [];
        $this->updatedProperties = [];
    }

    /**
     * @return array
     */
    public function getUpdatedFields(): array
    {
        $fields = [];
        foreach ($this->updatedFields as $field) {
            $action = 'get' . ucfirst($field);
            $fields[self::toSnakeCase($field)] = $this->$action();
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        $fields = ['IBLOCK_ID' => self::iblockId()];

        foreach ($this->fields as $field => $value) {
            $action = 'get' . ucfirst($field);
            $fields[self::toSnakeCase($field)] = $value;
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getUpdatedProperties(): array
    {
        $properties = [];
        foreach ($this->updatedProperties as $field) {
            $action = 'get' . ucfirst($field);
            $properties[self::toSnakeCase($field)] = $this->$action();
        }

        return $properties;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        $properties = [];
        foreach ($this->properties as $field => $value) {
            $action = 'get' . ucfirst($field);
            $properties[self::toSnakeCase($field)] = $this->$action();
        }

        return $properties;
    }

    public function getId()
    {
        return $this->getField('ID');
    }

    public function getXmlId()
    {
        return $this->getField('XML_ID');
    }

    public function setXmlId($xmlId)
    {
        return $this->setField('XML_ID', $xmlId);
    }

    public function getName()
    {
        return $this->getField('NAME');
    }

    public function setName($value)
    {
        return $this->setField('NAME', $value);
    }

    public function getField($name)
    {
        return $this->fields[$this->toCamelCase($name)];
    }

    public function setField($name, $value)
    {
        return $this->fields[$this->toCamelCase($name)] = $value;
    }

    public function __call($name, $arguments)
    {
        $field = lcfirst($name);
        $action = substr($name, 0, 3);
        $field = substr($name, 3, strlen($name));
        //$field = $this->toCamelCase($field);
        $field = lcfirst($field);

        if ($action == 'get') {
            return $this->__get($field);
        }

        if ($action == 'set') {
            return $this->__set($field, $arguments[0]);
        }

        return null;
    }

    public function __set($name, $value)
    {
        $name = $this->toCamelCase($name);

        if (isset($this->fields[$name])) {
            $this->fields[$name] = $value;
            $this->updatedFields[] = $name;
        } else {
            $this->properties[$name] = $value;
            $this->updatedProperties[] = $name;
        }

        return $this;
    }

    public function __get($name)
    {
        $result = null;
        
        $property = 'value';
        if (mb_substr($name, -5) == 'xmlid' || mb_substr($name, -5) == 'XmlId') {
            $property = mb_strtolower(mb_substr($name, -5));
            $name = mb_substr($name, 0, strlen($name) - 5);
        }

        if (mb_substr($name, -4) == 'Code') {
            $property = mb_strtolower(mb_substr($name, -4));
            $name = mb_substr($name, 0, strlen($name) - 4);
        }

        if (mb_substr($name, -4) == 'Name') {
            $property = mb_strtolower(mb_substr($name, -4));
            $name = mb_substr($name, 0, strlen($name) - 4);
        }

        if (isset($this->fields[$name])) {
            $result = $this->fields[$name];
        } elseif (isset($this->properties[$name])) {
            $result = $this->properties[$name][$property];
        }

        return $result;
    }

    public function toArray(): array
    {
        return $this->originData;
    }
}
