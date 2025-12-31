<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Requests;

use App\Http\Api\Shered\Request\ApiRequest;
use Domain\Drug\DrugId;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationDate;
use Domain\MedicationHistory\UserId;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'create_medication_history_request',
    properties: [
        new Property(property: 'drug_id', type: 'integer'),
        new Property(property: 'user_id', type: 'integer'),
        new Property(property: 'amount', type: 'integer'),
        new Property(property: 'medication_date', type: 'string'),
    ]
)]
class CreateMedicationHistoryRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'drug_id' => 'required|integer',
            'user_id' => 'required|integer',
            'amount' => 'required|integer',
            'medication_date' => 'required|date',
        ];
    }

    public function getDrugId(): DrugId
    {
        return new DrugId($this->integer('drug_id'));
    }

    public function getUserId(): UserId
    {
        return new UserId($this->integer('user_id'));
    }

    public function getAmount(): Amount
    {
        return new Amount($this->integer('amount'));
    }

    public function getMedicationData(): MedicationDate
    {
        return MedicationDate::forStringTime($this->input('medication_date'));
    }
}
