<?php

namespace BitrixModels\Model;

class Pagination implements \JsonSerializable
{
    protected $currentPage = 1;
    protected $perPage = 20;
    protected $countElements = 0;
    protected $totalItems = 0;

    public function __construct($page = 1, $count = 20, $countElements = 0, $totalItems = 0)
    {
        $this->setCurrentPage((int)$page);
        $this->setPerPage((int)$count);
        $this->setCountElements((int)$countElements);
        $this->setTotalItems((int)$totalItems);
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * @return int
     */
    public function getCountElements(): int
    {
        return $this->countElements;
    }

    /**
     * @param int $countElements
     */
    public function setCountElements(int $countElements): void
    {
        $this->countElements = $countElements;
    }

    /**
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * @param int $totalItems
     */
    public function setTotalItems(int $totalItems): void
    {
        $this->totalItems = $totalItems;
    }

    public function jsonSerialize()
    {
        return [
            //'currentPage' => $this->getCurrentPage(),
            //'perPage' => $this->getPerPage(),
            //'totalItems' => $this->getTotalItems(),
            'page' => $this->getCurrentPage(),
            'count' => $this->getPerPage(),
            'countPages' => $this->getCountElements(),
            'countElements' => $this->getTotalItems(),
        ];
    }
}
