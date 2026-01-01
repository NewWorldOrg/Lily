<?php

declare(strict_types=1);

namespace App\DataTransfer\MedicationHistory;

use App\DataTransfer\BaseApiPaginator;
use Domain\Common\Paginator\Paginate;
use Domain\MedicationHistory\MedicationHistoryCount;
use OpenApi\Attributes\Schema;

#[Schema(schema: 'medication_history_list_result', allOf: [new Schema('#/components/schemas/base_api_paginator')])]
class MedicationHistoryListResult extends BaseApiPaginator
{
    public function __construct(
        MedicationHistoryDetailList $medicationHistoryDetailList,
        MedicationHistoryCount $medicationHistoryCount,
        Paginate $paginate,
    ) {
        parent::__construct(
            $medicationHistoryDetailList->toArray(),
            $medicationHistoryCount->getRawValue(),
            $paginate->getPerPage()->getRawValue(),
        );
    }
}
