<?php

namespace BitrixModels\Model;

class Sort implements \JsonSerializable
{
    const DESC = 'DESC';
    const ASC = 'ASC';

    protected $sortBy;
    protected $sortDirection;

    public function __construct($sortBy = null, $sortDirection = null)
    {
        $this->setSortBy($sortBy);
        $this->setSortDirection($sortDirection);
    }

    /**
     * @return mixed
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy): self
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param mixed $sortDirection
     */
    public function setSortDirection($sortDirection): self
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    public function getResult() : array
    {
        if ($this->getSortBy()) {
            return [$this->getSortBy() => $this->getSortDirection()];
        }

        return [];
    }

    public function jsonSerialize()
    {
        return [
            'sortBy' => $this->getSortBy(),
            'sortDirection' => $this->getSortDirection(),
        ];
    }
}
