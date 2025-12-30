<?php

declare(strict_types=1);

namespace Domain\MedicationHistory;

use Domain\Common\Paginator\Paginate;
use Domain\Common\RawPositiveInteger;
use Domain\Drug\DrugId;

interface MedicationHistoryRepository
{
    public function get(MedicationHistoryId $id): MedicationHistory;
    public function getPaginator(Paginate $paginate): MedicationHistoryList;
    public function getPaginatorByUserId(Paginate $paginate, UserId $userId): MedicationHistoryList;
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
