<?php

declare(strict_types=1);

namespace Infra\EloquentModels;

use Domain\Drug\Drug as DrugDomain;
use Domain\Drug\DrugId;
use Domain\Drug\DrugName;
use Domain\Drug\DrugUrl;
use Illuminate\Database\Eloquent\Builder;
use Infra\EloquentModels\Model as AppModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $drug_name
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at 作成日時
 * @property \Illuminate\Support\Carbon|null $updated_at 更新日時
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Infra\EloquentModels\MedicationHistory> $medicationHistories
 * @property-read int|null $medication_histories_count
 * @method static Builder<static>|Drug newModelQuery()
 * @method static Builder<static>|Drug newQuery()
 * @method static Builder<static>|Drug orWhereLike(string $attribute, string $keyword, int $position = 0)
 * @method static Builder<static>|Drug query()
 * @method static Builder<static>|Drug sortSetting(string $orderBy, string $sortOrder, string $defaultKey = 'id')
 * @method static Builder<static>|Drug whereCreatedAt($value)
 * @method static Builder<static>|Drug whereDrugName($value)
 * @method static Builder<static>|Drug whereId($value)
 * @method static Builder<static>|Drug whereLike(string $attribute, string $keyword, int $position = 0)
 * @method static Builder<static>|Drug whereUpdatedAt($value)
 * @method static Builder<static>|Drug whereUrl($value)
 * @mixin \Eloquent
 */
class Drug extends AppModel
{

    protected $table = 'drugs';

    protected $guarded = [
        'id',
    ];

    public static array $sortable = [
        'id',
        'drug_name',
    ];

    /**
     * @return HasMany
     */
    public function medicationHistories(): HasMany
    {
        return $this->hasMany('Infra\EloquentModels\MedicationHistory', 'drug_id');
    }

    /**
     * Sort
     *
     * @param Builder $query
     * @param string $orderBy
     * @param string $sortOrder
     * @param string $defaultKey
     * @return mixed
     */
    public static function scopeSortSetting(
        Builder $query,
        string $orderBy,
        string $sortOrder,
        string $defaultKey = 'id',
    ): Builder {
        return AppModel::commonSortSetting(
            $query,
            self::$sortable,
            $orderBy,
            $sortOrder,
            $defaultKey
        );
    }

    public function toDomain(): DrugDomain
    {
        return new DrugDomain(
            new DrugId($this->id),
            new DrugName($this->drug_name),
            new DrugUrl($this->url)
        );
    }

}
