<?php

namespace BitrixModels\Entity;

class HighloadModel extends BaseModel
{
    protected $fields = [
        'id' => null,
        'ufXmlId' => null,
    ];

    protected $updatedFields = [];

    public function mapData($data = []): self
    {
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
            $fields[self::toSnakeCase($field)] = $value;
        }

        return $fields;
    }

    public function getField($name)
    {
        return $this->__get($this->toCamelCase($name));
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

        $this->fields[$name] = $value;
    }

    public function __get($name)
    {
        $result = null;

        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        $name = $this->toCamelCase($name);

        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        }

        return null;
    }

    public function toArray(): array
    {
        return [];
    }
}
