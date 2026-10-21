<?php

namespace Infra\EloquentModels;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Builder;

abstract class Model extends EloquentModel
{
    public function scopeWhereLike(
        Builder $query,
        string $attribute,
        string $keyword,
        int $position = 0,
    ): Builder {
        $keyword = addcslashes($keyword, '\_%');

        $condition = [
                1  => "{$keyword}%",
                -1 => "%{$keyword}",
            ][$position] ?? "%{$keyword}%";

        return $query->where($attribute, 'LIKE', $condition);
    }

    public function scopeOrWhereLike(
        Builder $query,
        string $attribute,
        string $keyword,
        int $position = 0,
    ): Builder {
        $keyword = addcslashes($keyword, '\_%');

        $condition = [
                1  => "{$keyword}%",
                -1 => "%{$keyword}",
            ][$position] ?? "%{$keyword}%";

        return $query->orWhere($attribute, 'LIKE', $condition);
    }

    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d: H:i:s');
    }

    /**
     * Common sort setting
     *
     * @param Builder $query
     * @param array $sortable
     * @param string $orderBy
     * @param string $sortOrder
     * @param string $defaultKey
     * @return Builder
     */
    protected static function commonSortSetting(
        Builder $query,
        array $sortable,
        string $orderBy,
        string $sortOrder,
        string $defaultKey,
    ): Builder {
        foreach ($sortable as $key => $value) {
            if (is_int($key) && $value === $orderBy) {
                $sortOrder = (strtolower($sortOrder) != 'desc') ? 'asc' : 'desc';
                return $query->orderBy($orderBy, $sortOrder);
            }
        }
        return $query->orderBy($defaultKey);
    }
}
