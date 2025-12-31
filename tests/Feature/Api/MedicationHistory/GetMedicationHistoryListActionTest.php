<?php

declare(strict_types=1);

namespace Tests\Feature\Api\MedicationHistory;

use Tests\Feature\FeatureTestCase;

class GetMedicationHistoryListActionTest extends FeatureTestCase
{
    private string $targetApi = '/api/medication_histories?user_id=1';

    public function testInvoke(): void
    {
        $this->getJson($this->targetApi)->assertOk();
    }
}
