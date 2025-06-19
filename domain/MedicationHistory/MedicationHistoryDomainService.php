<?php

declare(strict_types=1);

namespace Domain\MedicationHistory;

use Domain\Common\Paginator\Paginate;
use Domain\Common\RawPositiveInteger;
use Domain\Drug\DrugId;
use Domain\User\Id;
use Domain\User\UserDomainService;

class MedicationHistoryDomainService
{
    public function __construct(
        private MedicationHistoryRepository $medicationHistoryRepository,
        private UserDomainService $userDomainService,
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
        Amount $amount
    ): MedicationHistory {
        return $this->medicationHistoryRepository->create($userId, $drugId, $amount);
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
