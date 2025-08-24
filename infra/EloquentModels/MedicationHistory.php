<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Domain\Common\CreatedAt;
use Domain\Common\UpdatedAt;
use Domain\Drug\DrugId;
use Domain\MedicationHistory\MedicationDate;
use Domain\MedicationHistory\MedicationHistory as MedicationHistoryDomain;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationHistoryId;
use Domain\MedicationHistory\UserId;
use Infra\EloquentModels\Model as AppModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property int $drug_id
 * @property string $amount 服薬量
 * @property string $medication_date
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @property-read \Infra\EloquentModels\Drug $drug
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory newQuery()
 * @method static Builder<static>|MedicationHistory orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereDrugId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereId($value)
 * @method static Builder<static>|MedicationHistory whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereMedicationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MedicationHistory whereUserId($value)
 * @mixin \Eloquent
 */
class MedicationHistory extends AppModel
{
    protected $table = 'medication_histories';

    protected $guarded = [
        'id',
        'user_id',
        'drug_id',
    ];

    public $sortable = [
        'id',
        'name',
        'drug_name',
    ];

    public function drug(): BelongsTo
    {
        return $this->belongsTo(Drug::class, 'drug_id');
    }

    public function toDomain(): MedicationHistoryDomain
    {
        return new MedicationHistoryDomain(
            new MedicationHistoryId((int)$this->id),
            new UserId((int)$this->user_id),
            new DrugId((int)$this->drug_id),
            new Amount((float)$this->amount),
            MedicationDate::forStringTime((string)$this->medication_date),
            CreatedAt::forStringTime((string)$this->created_at),
            UpdatedAt::forStringTime((string)$this->updated_at),
        );
    }
}
