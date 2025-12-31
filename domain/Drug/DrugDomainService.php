<?php

declare(strict_types=1);

namespace Domain\Drug;

use Domain\Common\Paginator\Paginate;

class DrugDomainService
{
    private DrugRepository $drugRepository;

    public function __construct(DrugRepository $drugRepository)
    {
        $this->drugRepository = $drugRepository;
    }

    public function get(DrugId $drugId): Drug
    {
        return $this->drugRepository->get($drugId);
    }

    public function getDrugList(): DrugList
    {
        return $this->drugRepository->getDrugs();
    }

    public function findDrugByName(DrugName $drugName): Drug
    {
        return $this->drugRepository->findDrugByName($drugName);
    }

    public function getPaginator(Paginate $paginate): DrugList
    {
        return $this->drugRepository->getPaginator($paginate);
    }

    public function createDrug(DrugName $drugName, DrugUrl $drugUrl): Drug
    {
        return $this->drugRepository->create($drugName, $drugUrl);
    }

    public function updateDrug(Drug $drug): Drug
    {
        return $this->drugRepository->update($drug);
    }

    public function deleteDrug(DrugId $drugId): void
    {
        $this->drugRepository->delete($drugId);
    }
}
