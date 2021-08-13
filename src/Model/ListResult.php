<?php

namespace BitrixModels\Model;

class ListResult implements \JsonSerializable
{
    protected $list;

    /** @var Pagination */
    protected $pagination;

    public function setList(array $list)
    {
        $this->list = $list;
    }

    public function getList()
    {
        return $this->list;
    }

    public function setPagination(Pagination $pagination)
    {
        $this->pagination = $pagination;
    }

    public function getPagination()
    {
        return $this->pagination;
    }

    public function jsonSerialize()
    {
        return [
            'list' => $this->getList(),
            'pagination' => $this->getPagination()
        ];
    }
}
