<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Drugs;

use Infra\EloquentModels\Drug;
use App\Http\Requests\Request as AppRequest;
use Domain\Drug\DrugName;
use Domain\Drug\DrugUrl;

class CreateDrugRequest extends AppRequest
{
    /**
     * check authorize
     * @return bool
     */
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
            'drug_name' => 'required|string|max:255',
            'url' => 'required|url',
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
            'drug_name.required' => '薬物名は必須です',
            'url.required' => 'URLは必須です',
            'url.url' => '有効なURLではありません',
        ];
    }

    public function getDrugName(): DrugName
    {
        return new DrugName($this->input('drug_name'));
    }

    public function getUrl(): DrugUrl
    {
        return new DrugUrl($this->input('url'));
    }
}
