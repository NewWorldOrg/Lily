<?php

declare(strict_types=1);

namespace Domain\Drug;

use Domain\Common\Paginator\Paginate;

interface DrugRepository
{
    public function get(DrugId $drugId): Drug;
    public function getDrugs(): DrugList;
    public function getCount(): DrugCount;
    public function findDrugByName(DrugName $drugName): Drug;
    public function getPaginator(Paginate $paginate): DrugList;
    public function create(DrugName $drugName, DrugUrl $drugUrl): Drug;
    public function update(Drug $drug): Drug;
    public function delete(DrugId $drugId): void;
}
