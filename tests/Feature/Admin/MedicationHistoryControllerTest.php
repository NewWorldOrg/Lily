<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\FeatureTestCase as TestCase;

class MedicationHistoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminLogin();
    }

    public function testIndex()
    {
        $this->get(route('admin.medication_histories.index'))->assertOk();
    }

    public function testEdit()
    {
        $this->get(route('admin.medication_histories.edit', 1))->assertOk();
    }

    public function testUpdate()
    {
        $params = [
            'amount' => 316,
        ];

        $this->post(route('admin.medication_histories.update', 1), $params)
            ->assertRedirect(route('admin.medication_histories.index'))
            ->assertSessionHas('success');
    }
}
