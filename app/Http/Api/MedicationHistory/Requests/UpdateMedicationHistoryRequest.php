<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Requests;

use App\Http\Api\Shered\Request\ApiRequest;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationDate;
use Domain\MedicationHistory\MedicationNote;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'update_medication_history_request',
    properties: [
        new Property(property: 'amount', type: 'number', format: 'double'),
        new Property(property: 'note', type: 'string', nullable: true),
        new Property(property: 'medication_date', type: 'string', format: 'date-time', nullable: true),
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
            'medication_date' => 'nullable|date',
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

    public function getMedicationDate(): ?MedicationDate
    {
        $value = $this->input('medication_date');
        if (is_null($value)) {
            return null;
        }
        return new MedicationDate($value);
    }
}
