<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Drug;

use Tests\Feature\FeatureTestCase;

class CreateDrugActionTest extends FeatureTestCase
{
    private string $targetApi = '/api/drugs';

    public function testInvoke(): void
    {
        $params = [
            'drug_name' => '高田憂希',
            'url' => 'https://takada-yuki.test/',
        ];

        $this->post($this->targetApi, $params)->assertOk();
    }
}
