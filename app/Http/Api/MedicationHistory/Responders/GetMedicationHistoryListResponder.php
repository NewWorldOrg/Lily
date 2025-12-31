<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Responders;

use App\DataTransfer\MedicationHistory\MedicationHistoryListResult;
use App\Http\Api\Common\Responder\BaseResponder;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'get_medication_history_list_responder',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class GetMedicationHistoryListResponder extends BaseResponder
{
    #[Property(
        property: 'data',
        properties: [
            new Property(
                property: 'medicationHistories',
                ref: '#/components/schemas/medication_history_list_result',
                type: 'object'
            )
       ]
    )]
    public array $medicationHistories;

    public function __construct(MedicationHistoryListResult $medicationHistoryListResult)
    {
        $this->medicationHistories = $medicationHistoryListResult->toArray();
    }
}
