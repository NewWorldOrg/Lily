<?php

declare(strict_types=1);

namespace Tests\Feature\Api\MedicationHistory;

use Tests\Feature\FeatureTestCase;

class CreateMedicationHistoryActionTest extends FeatureTestCase
{
    private string $targetApi = '/api/medication_histories';

    public function testInvoke(): void
    {
        $params = [
            'drug_id' => 1,
            'user_id' => 930316,
            'amount' => 20,
            'medication_date' => '2025-03-16 00:00:00',
        ];

        $this->postJson($this->targetApi, $params)->assertOk();
    }
}
