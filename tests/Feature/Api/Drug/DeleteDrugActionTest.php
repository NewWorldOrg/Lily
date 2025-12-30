<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Drug;

use Tests\Feature\FeatureTestCase;

class DeleteDrugActionTest extends FeatureTestCase
{
    private string $targetApi = '/api/drugs/';

    public function testInvoke(): void
    {
        $this->delete($this->targetApi . 1)->assertOk();
    }
}
