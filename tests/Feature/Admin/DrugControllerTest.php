<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\FeatureTestCase;

class DrugControllerTest extends FeatureTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminLogin();
    }

    public function testIndex()
    {
        $this->get(route('admin.drugs.index'))->assertOk();
    }

    public function testCreate()
    {
        $this->get(route('admin.drugs.create'))->assertOk();
    }

    public function testStore()
    {
        $params = [
            'drug_name' => 'レンボレキサント',
            'url' => 'https://ja.wikipedia.org/wiki/%E3%83%AC%E3%83%B3%E3%83%9C%E3%83%AC%E3%82%AD%E3%82%B5%E3%83%B3%E3%83%88',
        ];

        $this->post(route('admin.drugs.store'), $params)
            ->assertRedirect(route('admin.drugs.index'))
            ->assertSessionHas('success');;
    }

    public function testEdit()
    {
        $this->get(route('admin.drugs.edit', 1))->assertOk();
    }

    public function testUpdate()
    {
        $params = [
            'drug_name' => '高田憂希',
            'url' => 'https://ja.wikipedia.org/wiki/%E9%AB%98%E7%94%B0%E6%86%82%E5%B8%8C',
        ];

        $this->post(route('admin.drugs.update', 1), $params)
            ->assertRedirect(route('admin.drugs.index'))
            ->assertSessionHas('success');
    }

    public function testDelete()
    {
        $this->post(route('admin.drugs.delete', 1))
            ->assertRedirect(route('admin.drugs.index'))
            ->assertSessionHas('success');
    }
}
