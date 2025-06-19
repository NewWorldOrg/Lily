<?php

namespace App\Providers;

use Domain\AdminUser\AdminUserRepository;
use Domain\User\UserRepository;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \Infra\EloquentRepository\AdminUserRepository::class => \App\Policies\AdminUserPolicy::class,
        \Infra\EloquentRepository\DrugRepository::class => \App\Policies\DrugPolicy::class,
        \Infra\EloquentRepository\MedicationHistoryRepository::class => \App\Policies\MedicationHistoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(AuthManager $authManager)
    {
        $this->registerPolicies();

        $authManager->provider('adminAuth', function($app) {
            return new AdminUserProvider(
                $app->make(AdminUserRepository::class),
                $app['hash'],
            );
        });
    }
}
