<?php

declare(strict_types=1);

namespace App\DataTransfer\MedicationHistory;

use Domain\Drug\Drug;
use Domain\MedicationHistory\MedicationHistory;

class MedicationHistoryDetail
{
    public function __construct(
        private MedicationHistory $medicationHistory,
        private Drug $drug,
    ){
    }

    /**
     * @return MedicationHistory
     */
    public function getMedicationHistory(): MedicationHistory
    {
        return $this->medicationHistory;
    }

    /**
     * @return Drug
     */
    public function getDrug(): Drug
    {
        return $this->drug;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->medicationHistory->getId()->getRawValue(),
            'user'=> $this->user->toArray(),
            'amount' => $this->medicationHistory->getAmount()->getRawValue(),
            'drug' => $this->drug->toArray(),
        ];
    }
}
