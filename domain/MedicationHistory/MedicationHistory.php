<?php

declare(strict_types=1);

namespace Domain\MedicationHistory;

use Domain\Common\CreatedAt;
use Domain\Common\UpdatedAt;
use Domain\Drug\DrugId;

readonly class MedicationHistory
{
    public function __construct(
        private MedicationHistoryId $id,
        private UserId $userId,
        private DrugId $drugId,
        private Amount $amount,
        private MedicationDate $medicationDate,
        private CreatedAt $createdAt,
        private UpdatedAt $updatedAt,
    ) {
    }

    public function getId(): MedicationHistoryId
    {
        return $this->id;
    }

    public function getUserId(): UserId
    {
        return $this->userId;
    }

    public function getDrugId(): DrugId
    {
        return $this->drugId;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getMedicationDate(): MedicationDate
    {
        return $this->medicationDate;
    }

    public function getCreatedAt(): CreatedAt
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getRawValue(),
            'userId' => $this->getUserId()->getRawValue(),
            'drugId' => $this->getDrugId()->getRawValue(),
            'amount' => $this->getAmount()->getRawValue(),
            'createdAt' => $this->createdAt->getDetail(),
        ];
    }
}
