<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Responders;

use App\DataTransfer\MedicationHistory\MedicationHistoryDetail;
use App\Http\Api\Common\Responder\BaseResponder;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'create_medication_history_responder',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class CreateMedicationHistoryResponder extends BaseResponder
{
    #[Property(
        property: 'data',
        properties: [
            new Property(
                property: 'drug',
                ref: '#/components/schemas/medication_history_detail',
                type: 'object',
            )
        ],
    )]
    public function __construct(public readonly MedicationHistoryDetail $medicationHistory)
    {
    }
}
