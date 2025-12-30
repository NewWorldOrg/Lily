<?php

declare(strict_types=1);

namespace App\DataTransfer\Drug;

use Domain\Drug\Drug;
use Domain\Drug\DrugId;
use Domain\Drug\DrugName;
use Domain\Drug\DrugUrl;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(schema: 'drug_detail', required: ['id', 'name', 'url'])]
class DrugDetail
{
    #[Property(type: 'integer', example: 1)]
    public DrugId $id;
    #[Property(type: 'string', example: '高田憂希')]
    public DrugName $name;
    #[Property(type: 'string', example: 'https://example.com/')]
    public DrugUrl $url;

    public function __construct(Drug $drug)
    {
        $this->id = $drug->getId();
        $this->name = $drug->getName();
        $this->url = $drug->getUrl();
    }
}
