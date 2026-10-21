<?php

declare(strict_types=1);

namespace App\DataTransfer\Drug;

use Domain\Common\Paginator\Paginate;
use Domain\Drug\DrugCount;
use Domain\Drug\DrugList;
use Illuminate\Pagination\LengthAwarePaginator;

class DrugPaginator extends LengthAwarePaginator
{
    public function __construct(
        DrugList $drugList,
        DrugCount $drugCount,
        Paginate $paginate,
    ) {
        parent::__construct(
            $drugList,
            $drugCount->getRawValue(),
            $paginate->getPerPage()->getRawValue(),
        );
    }
}
