<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use Domain\AdminUser\AdminUserId;
use Domain\AdminUser\AdminUserRole;
use Domain\AdminUser\AdminUserStatus;
use Domain\Common\RawString;
use Domain\Exception\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Infra\EloquentModels\AdminUser as AdminUserModel;
use Infra\EloquentRepository\AdminUserRepository;
use Tests\Feature\FeatureTestCase as TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;
    private AdminUserRepository $adminUserRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->adminLogin();
        $this->adminUserRepository = $this->app->make(AdminUserRepository::class);
    }

    public function testIndex()
    {
        $this->get(route('admin.admin_users.index'))->assertOk();
    }

    public function testCreate()
    {
        $this->get(route('admin.admin_users.create'))->assertOk();
    }

    public function testStore()
    {
        $params = [
            'user_id' => 'test_takada_yuki',
            'password' => 'hogehoge',
            'password_confirm' => 'hogehoge',
            'name' => '高田憂希',
            'role' => 1,
            'status' => 1,
        ];

        $this->post(route('admin.admin_users.store'), $params)
            ->assertRedirect(route('admin.admin_users.index'))
            ->assertSessionHas('success');

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $this->assertTrue(
            $response->getUserId()->isEqual(new AdminUserId($params['user_id']))
        );
    }

    public function testEdit()
    {
        $this->get(route('admin.admin_users.edit', $this->adminUser->getId()))->assertOk();
    }

    public function testUpdate()
    {
        $params = [
            'user_id' => 'test_takada_yuki',
            'password' => 'hogehoge',
            'name' => '高田憂希',
            'role' => AdminUserRole::ROLE_SYSTEM->getValue()->getRawValue(),
            'status' => AdminUserStatus::STATUS_VALID->getValue()->getRawValue(),
        ];

        AdminUserModel::create($params);

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $params['user_id'] = 'takada_yuki_test';
        $params['password_confirm'] = 'hogehoge';

        $this->put(route('admin.admin_users.update', $response->getId()), $params)
            ->assertRedirect(route('admin.admin_users.index'))
            ->assertSessionHas('success');

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $this->assertTrue(
            $response->getUserId()->isEqual(new AdminUserId($params['user_id']))
        );

        $this->get(route('admin.auth.logout'));

        $this->post('/admin/auth/login', [
            'user_id' => 'takada_yuki_test',
            'password' => 'hogehoge',
        ])
            ->assertRedirect(route('admin.top_page'));
    }

    public function testDestroy()
    {
        $params = [
            'user_id' => 'test_takada_yuki',
            'password' => 'hogehoge',
            'name' => '高田憂希',
            'role' => 1,
            'status' => 1,
        ];

        AdminUserModel::create($params);

        $response = $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));

        $this->delete(route('admin.admin_users.destroy', $response->getId()))
            ->assertRedirect(route('admin.admin_users.index'))
            ->assertSessionHas('success');

        $this->expectException(NotFoundException::class);
        $this->adminUserRepository->getByUserId(new AdminUserId($params['user_id']));
    }
}
