<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Responders;

use App\DataTransfer\MedicationHistory\MedicationHistoryDetail;
use App\Http\Api\Common\Responder\BaseResponder;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'update_medication_history_responder',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class UpdateMedicationHistoryResponder extends BaseResponder
{
    #[Property(
        property: 'data',
        ref: '#/components/schemas/medication_history_detail',
        type: 'object',
    )]
    public function __construct(public readonly MedicationHistoryDetail $data)
    {
    }
}
