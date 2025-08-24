<?php

declare(strict_types=1);

namespace Domain\MedicationHistory;

use Domain\Common\Paginator\Paginate;
use Domain\Common\RawPositiveInteger;
use Domain\Drug\DrugId;
use Domain\User\Id;

interface MedicationHistoryRepository
{
    public function getPaginator(Paginate $paginate): MedicationHistoryList;
    public function getCount(): MedicationHistoryCount;
    public function getCountMedicationTake(DrugId $drugId): RawPositiveInteger;
    public function create(
        UserId $userId,
        DrugId $drugId,
        Amount $amount,
        MedicationDate $medicationDate,
    ): MedicationHistory;
    public function update(MedicationHistory $medicationHistory): MedicationHistory;
    public function delete(MedicationHistoryId $id): RawPositiveInteger;
}
