<?php

declare(strict_types=1);

namespace Infra\EloquentRepository;

use Domain\Common\OrderKey;
use Domain\Common\Paginator\Paginate;
use Domain\Drug\Drug;
use Domain\Drug\DrugCount;
use Domain\Drug\DrugId;
use Domain\Drug\DrugList;
use Domain\Drug\DrugName;
use Domain\Drug\DrugRepository as DrugRepositoryInterface;
use Domain\Drug\DrugUrl;
use Domain\Exception\LogicException;
use Domain\Exception\NotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Infra\EloquentModels\Drug as DrugModel;

class DrugRepository implements DrugRepositoryInterface
{
    public function get(DrugId $drugId): Drug
    {
        $model = DrugModel::where(['id' => $drugId->getRawValue()])->first();

        if (!$model) {
            throw new NotFoundException();
        }

        return $model->toDomain();
    }

    public function getDrugs(): DrugList
    {
        $builder = DrugModel::sortSetting('id', 'desc');
        /* @var $collection Collection */
        $collection = $builder->get();

        return new DrugList($collection->map(function ($model) {
            /** @var $model DrugModel */
            return $model->toDomain();
        })->toArray());
    }

    public function getCount(): DrugCount
    {
        $query = DrugModel::query();
        return new DrugCount($query->count());
    }

    public function findDrugByName(DrugName $drugName): Drug
    {
        $model = DrugModel::where(['drug_name' => $drugName->getRawValue()])->first();

        if (!$model) {
            throw new NotFoundException();
        }

        return $model->toDomain();
    }

    public function getPaginator(Paginate $paginate): DrugList
    {
        $builder = DrugModel::query()
            ->orderBy('id', OrderKey::DESC->getValue()->getRawValue())
            ->limit($paginate->getLimit()->getRawValue())
            ->offset($paginate->offset()->getRawValue());

        $collection = $builder->get();

        return new DrugList($collection->map(function(DrugModel $model) {
            return $model->toDomain();
        })->toArray());
    }

    public function create(DrugName $drugName, DrugUrl $drugUrl): Drug
    {
        $model = new DrugModel;
        $model->drug_name = $drugName->getRawValue();
        $model->url = $drugUrl->getRawValue();

        $model->save();

        return $model->toDomain();
    }

    public function update(Drug $drug): Drug
    {
        $model = DrugModel::where(['id' => $drug->getId()->getRawValue()])->first();
        $model->drug_name = $drug->getName()->getRawValue();
        $model->url = $drug->getUrl()->getRawValue();

        $model->save();

        return $model->toDomain();
    }

    public function delete(DrugId $drugId): void
    {
        $model = DrugModel::where(['id' => $drugId->getRawValue()])->first();

        if (!$model) {
            throw new NotFoundException();
        }

        $result = $model->delete();

        if (!$result) {
            throw new LogicException();
        }
    }
}
