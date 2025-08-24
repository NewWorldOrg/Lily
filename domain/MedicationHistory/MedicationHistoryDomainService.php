<?php

declare(strict_types=1);

namespace Domain\MedicationHistory;

use Domain\Common\Paginator\Paginate;
use Domain\Common\RawPositiveInteger;
use Domain\Drug\DrugId;

class MedicationHistoryDomainService
{
    public function __construct(
        private MedicationHistoryRepository $medicationHistoryRepository,
    ) {
    }

    public function getPaginate(Paginate $paginate): MedicationHistoryList
    {
        return $this->medicationHistoryRepository->getPaginator($paginate);
    }

    public function getCountMedicationTake(DrugId $drugId): RawPositiveInteger
    {
        return $this->medicationHistoryRepository->getCountMedicationTake($drugId);
    }

    public function create(
        UserId $userId,
        DrugId $drugId,
        Amount $amount,
        ?MedicationDate $medicationDate,
    ): MedicationHistory {
        if (is_null($medicationDate)) {
            $medicationDate = MedicationDate::now();
        }

        return $this->medicationHistoryRepository->create($userId, $drugId, $amount, $medicationDate);
    }

    public function update(MedicationHistory $medicationHistory): MedicationHistory
    {
        return $this->medicationHistoryRepository->update($medicationHistory);
    }

    public function delete(MedicationHistoryId $id): RawPositiveInteger
    {
        return $this->medicationHistoryRepository->delete($id);
    }
}
