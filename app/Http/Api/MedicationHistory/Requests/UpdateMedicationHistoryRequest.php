<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Requests;

use App\Http\Api\Shered\Request\ApiRequest;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationNote;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'update_medication_history_request',
    properties: [
        new Property(property: 'amount', type: 'number', format: 'double'),
        new Property(property: 'note', type: 'string', nullable: true),
    ]
)]
class UpdateMedicationHistoryRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'note' => 'nullable|string',
        ];
    }

    public function getAmount(): Amount
    {
        return new Amount((float)$this->input('amount'));
    }

    public function getNote(): MedicationNote
    {
        return new MedicationNote($this->input('note'));
    }
}
