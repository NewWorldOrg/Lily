<?php

declare(strict_types=1);

namespace Api\MedicationHistory;

use Tests\Feature\FeatureTestCase;

class GetMedicationHistoryActionTest extends FeatureTestCase
{
    private string $targetApi = '/api/medication_histories/';

    public function testInvoke(): void
    {
        $this->get($this->targetApi . 1)->assertOk();
    }
}
