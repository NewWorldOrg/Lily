<?php

declare(strict_types=1);

namespace Infra\EloquentRepository;

use Domain\Common\OrderKey;
use Domain\Common\Paginator\Paginate;
use Domain\Common\RawPositiveInteger;
use Domain\Drug\DrugId;
use Domain\Exception\LogicException;
use Domain\Exception\NotFoundException;
use Domain\MedicationHistory\MedicationHistory;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationHistoryCount;
use Domain\MedicationHistory\MedicationHistoryId;
use Domain\MedicationHistory\MedicationHistoryList;
use Domain\MedicationHistory\MedicationHistoryRepository as MedicationHistoryRepositoryInterface;
use Domain\MedicationHistory\UserId;
use Infra\EloquentModels\MedicationHistory as MedicationHistoryModel;

class MedicationHistoryRepository implements MedicationHistoryRepositoryInterface
{
    private const WITH_MODEL = [
        'drug',
    ];

    public function getPaginator(Paginate $paginate): MedicationHistoryList
    {
        $builder = MedicationHistoryModel::with(self::WITH_MODEL)
            ->orderBy('id', OrderKey::DESC->getValue()->getRawValue())
            ->limit($paginate->getLimit()->getRawValue())
            ->offset($paginate->offset()->getRawValue());

        $collection = $builder->get();

        return new MedicationHistoryList($collection->map(function(MedicationHistoryModel $model) {
            return $model->toDomain();
        })->toArray());
    }

    public function getCount(): MedicationHistoryCount
    {
        $query = MedicationHistoryModel::query();
        return new MedicationHistoryCount($query->count());
    }

    public function getCountMedicationTake(DrugId $drugId): RawPositiveInteger
    {
        return new RawPositiveInteger(
            MedicationHistoryModel::where(['drug_id' => $drugId->getRawValue()])->count()
        );
    }

    public function create(UserId $userId, DrugId $drugId, Amount $amount): MedicationHistory
    {
        $model = new MedicationHistoryModel();

        $model->user_id = $userId->getRawValue();
        $model->drug_id = $drugId->getRawValue();
        $model->amount = $amount->getRawValue();

        $model->save();

        return $model->toDomain();
    }

    public function update(MedicationHistory $medicationHistory): MedicationHistory
    {
        $model = MedicationHistoryModel::where([
            'id' => $medicationHistory->getId()->getRawValue()
        ])->first();

        $model->amount = $medicationHistory->getAmount()->getRawValue();

        $model->save();

        return $model->toDomain();
    }

    public function delete(MedicationHistoryId $id): RawPositiveInteger
    {
        $model = MedicationHistoryModel::where(['id' => $id->getRawValue()]);

        if (!$model) {
            throw new NotFoundException();
        }

        $result = $model->delete();

        if (!$result) {
            throw new LogicException();
        }

        return new RawPositiveInteger($result);
    }
}
