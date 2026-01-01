<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Requests;

use App\Http\Api\Shered\Request\ApiRequest;
use Domain\MedicationHistory\UserId;

class GetMedicationHistoryListRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|int',
            'page' => 'nullable|int',
            'per_page' => 'nullable|int',
            'order_by' => 'nullable|string',
            'sort' => 'nullable|string',
        ];
    }

    public function getUserId(): UserId
    {
        return new UserId($this->integer('user_id'));
    }
}
