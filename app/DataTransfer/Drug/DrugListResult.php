<?php

declare(strict_types=1);

namespace App\DataTransfer\Drug;

use App\DataTransfer\BaseApiPaginator;
use Domain\Common\Paginator\Paginate;
use Domain\Drug\DrugCount;
use Domain\Drug\DrugList;
use OpenApi\Attributes\Schema;

#[Schema(schema: 'drug_list_result', allOf: [new Schema('#/components/schemas/base_api_paginator')])]
class DrugListResult extends BaseApiPaginator
{
    public function __construct(
        DrugList $drugList,
        DrugCount $count,
        Paginate $paginate,
    ) {
        parent::__construct(
            $drugList->toArray(),
            $count->getRawValue(),
            $paginate->getPerPage()->getRawValue(),
            $paginate->getPage()->getRawValue(),
        );
    }
}
