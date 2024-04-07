<?php

namespace BitrixModels\Model;

use BitrixModels\Entity\BaseModel;

class ListResult implements \JsonSerializable
{
    protected array $list;

    /** @var Pagination */
    protected Pagination $pagination;

    public function setList(array $list)
    {
        $this->list = $list;
    }

    public function getList(): array
    {
        return $this->list;
    }

    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    public function jsonSerialize(): array
    {
        return [
            'list' => $this->getList(),
            'pagination' => $this->getPagination()
        ];
    }

    public function toArray(): array
    {
        /**
         * @var $row BaseModel
         */
        foreach ($this->getList() as $row) {
            $rows[] = $row->toArray();
        }

        return $rows ?? [];
    }
}
