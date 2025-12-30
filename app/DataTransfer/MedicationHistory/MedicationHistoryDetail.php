<?php

declare(strict_types=1);

namespace App\DataTransfer\MedicationHistory;

use Domain\Common\CreatedAt;
use Domain\Common\UpdatedAt;
use Domain\Drug\Drug;
use Domain\Drug\DrugId;
use Domain\Drug\DrugName;
use Domain\Drug\DrugUrl;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationHistory;
use Domain\MedicationHistory\MedicationHistoryId;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(schema: 'medication_history_detail', required: ['id', 'amount', 'drug', 'createdAt', 'updatedAt'])]
class MedicationHistoryDetail
{
    #[Property(type: 'integer', example: 1)]
    public MedicationHistoryId $id;
    #[Property(type: 'integer', example: 10)]
    public Amount $amount;
    #[Property(type: 'integer', example: 1)]
    public DrugId $drugId;
    #[Property(type: 'string', example: 'ラツーダ')]
    public DrugName $drugName;
    #[Property(type: 'string', example: 'https://example.com/')]
    public DrugUrl $drugUrl;
    #[Property(type: 'string', example: '2022-01-01T00:00:00+09:00')]
    public CreatedAt $createdAt;
    #[Property(type: 'string', example: '2022-03-16T00:00:00+09:00')]
    public UpdatedAt $updatedAt;

    public function __construct(
        private readonly MedicationHistory $medicationHistory,
        private readonly Drug $drug,
    ){
        $this->id = $this->medicationHistory->getId();
        $this->amount = $this->medicationHistory->getAmount();
        $this->drugId = $this->drug->getId();
        $this->drugName = $this->drug->getName();
        $this->drugUrl = $this->drug->getUrl();
        $this->createdAt = $this->medicationHistory->getCreatedAt();
        $this->updatedAt = $this->medicationHistory->getUpdatedAt();
    }
}
