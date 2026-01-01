<?php

declare(strict_types=1);

namespace Domain\Drug;

use Domain\Base\BaseHashMap;

class DrugHashMap extends BaseHashMap
{
    public function __construct(DrugList $drugList)
    {
        $a = [];

        foreach($drugList as $drug) {
            /** @var Drug $drug */
            $a[(string)$drug->getId()] = $drug;
        }

        parent::__construct($a);
    }
}
