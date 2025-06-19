<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\MedicationHistories;

use App\Http\Api\Shered\Request\ApiRequest;
use Domain\Drug\DrugName;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\UserId;

class CreateMedicationHistoryRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|numeric',
            'drug_name' => 'required|string',
            'amount' => 'required|numeric',
        ];
    }

    /**
     * messages
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'user_id.required' => 'ユーザーIDは必須です',
            'drug_name.required' => '薬物名は必須です',
            'amount.required' => '服薬量は必須です',
        ];
    }

    public function getUserId(): UserId
    {
        return new UserId((int)$this->input('user_id'));
    }

    public function getDrugName(): DrugName
    {
        return new DrugName($this->input('drug_name'));
    }

    public function getAmount(): Amount
    {
        return new Amount((float)$this->input('amount'));
    }
}
