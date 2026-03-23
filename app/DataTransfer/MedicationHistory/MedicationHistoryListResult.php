<?php

declare(strict_types=1);

namespace App\DataTransfer\MedicationHistory;

use App\DataTransfer\BaseApiPaginator;
use Domain\Common\Paginator\Paginate;
use Domain\MedicationHistory\MedicationHistoryCount;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'medication_history_list_result',
    allOf: [new Schema(ref: '#/components/schemas/base_api_paginator')],
    properties: [
        new Property(property: 'data', type: 'array', items: new Items(ref: '#/components/schemas/medication_history_detail')),
    ]
)]
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
