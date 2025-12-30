<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Requests;

use App\Http\Api\Shered\Request\ApiRequest;
use Domain\Drug\DrugId;

class ShowDrugRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function getDrugId(): DrugId
    {
        return new DrugId((int)$this->route('drugId'));
    }
}
