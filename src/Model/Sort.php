<?php

namespace BitrixModels\Model;

class Sort implements \JsonSerializable
{
    const DESC = 'DESC';
    const ASC = 'ASC';

    protected mixed $sortBy;
    protected mixed $sortDirection;

    protected array $list = [];

    public function __construct($sortBy = 'ID', $sortDirection = 'ASC')
    {
        $this->setSortBy($sortBy);
        $this->setSortDirection($sortDirection);
    }

    public static function create($sortBy = 'ID', $sortDirection = 'ASC'): Sort
    {
        return new Sort($sortBy, $sortDirection);
    }

    public function addSort($sortBy = 'ID', $sortDirection = 'ASC'): void
    {
        $this->list[] = self::create($sortBy, $sortDirection);
    }

    /**
     * @return mixed
     */
    public function getSortBy(): mixed
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $sortBy
     */
    public function setSortBy(mixed $sortBy): self
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortDirection(): mixed
    {
        return $this->sortDirection;
    }

    /**
     * @param mixed $sortDirection
     */
    public function setSortDirection(mixed $sortDirection): self
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    public function getResult() : array
    {
        if ($this->getSortBy()) {
            $result[$this->getSortBy()] = $this->getSortDirection();

            foreach ($this->list as $sortItem) {
                $result[$sortItem->getSortBy()] = $sortItem->getSortDirection();
            }

            return $result;
        }

        return [];
    }

    public function jsonSerialize(): array
    {
        return [
            'sortBy' => $this->getSortBy(),
            'sortDirection' => $this->getSortDirection(),
        ];
    }
}
