<?php

namespace BitrixModels\Helper;

use BitrixModels\Entity\BaseModel;

class HelperResultList
{
    public static function modelResultListToArray(\BitrixModels\Model\ListResult $listResult): array
    {
        /**
         * @var $row BaseModel
        */
        foreach ($listResult->getList() as $row) {
            $rows[] = $row->toArray();
        }

        return $rows ?? [];
    }
}
