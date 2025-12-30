<?php

declare(strict_types=1);

namespace App\DataTransfer;

use Illuminate\Pagination\LengthAwarePaginator;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;


#[Schema(
    schema: 'base_api_paginator',
    properties: [
        new Property(
            property: 'currentPage',
            type: 'integer',
            example: 1,
        ),
        new Property(
            property: 'lastPage',
            type: 'integer',
            example: 1,
        ),
        new Property(
            property: 'perPage',
            type: 'integer',
            example: 10,
        ),
        new Property(
            property: 'total',
            type: 'integer',
            example: 100,
        ),
        new Property(
            property: 'data',
            type: 'array',
            example: [],
        )
    ],
    type: 'object',
)]
abstract class BaseApiPaginator extends LengthAwarePaginator
{
    public function toArray(): array
    {
        return [
            'currentPage' => $this->currentPage(),
            'lastPage' => $this->lastPage(),
            'perPage' => $this->perPage(),
            'total' => $this->total(),
            'data' => $this->items->toArray(),
        ];
    }
}
