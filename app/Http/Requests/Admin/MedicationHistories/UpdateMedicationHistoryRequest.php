<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\MedicationHistories;

use App\Http\Requests\Request as AppRequest;
use Domain\MedicationHistory\Amount;
use Infra\EloquentModels\MedicationHistory;

class UpdateMedicationHistoryRequest extends AppRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric'
        ];
    }

    /**
     * message
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'amount.required' => '服薬量は必須です',
        ];
    }

    public function getAmount(): Amount
    {
        return new Amount((float)$this->input('amount'));
    }
}
