<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Drug;

use Tests\Feature\FeatureTestCase;

class GetDrugActionTest extends FeatureTestCase
{
    private string $targetApi = '/api/drugs/';

    public function testInvoke(): void
    {
        $this->get($this->targetApi . 1)->assertOk();
    }
}
