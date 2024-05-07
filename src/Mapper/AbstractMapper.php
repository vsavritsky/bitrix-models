<?php

namespace BitrixModels\Mapper;

use BitrixModels\Entity\BaseModel;

class AbstractMapper
{
    const MAPPER_CLASS = '';

    public function preload($items = []): void
    {

    }

    /**
     * @throws \Exception
     */
    public function map(BaseModel $item = null): array|null
    {
        if (!$item) {
            return null;
        }

        $this->preload([$item]);

        if (static::MAPPER_CLASS != get_class($item)) {
            throw new \Exception(sprintf('Маппер не для класса %s', get_class($item)));
        }

        return [];
    }

    /**
     * @throws \Exception
     */
    public function mapList(iterable $elements): array
    {
        $this->preload($elements);

        $result = [];
        foreach ($elements as $element) {
            $item = $this->map($element);
            if ($item) {
                $result[] = $item;
            }
        }

        return $result;
    }
}
