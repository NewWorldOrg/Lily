<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Drug;

use Tests\Feature\FeatureTestCase;

class GetDrugListActionTest extends FeatureTestCase
{
    public function testInvoke(): void
    {
        $this->json('GET', route('api.drugs.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'drugs' => [
                        'currentPage',
                        'lastPage',
                        'perPage',
                        'total',
                        'data',
                    ]
                ]
            ]);
    }
}
