<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Domain;
use Infra\EloquentRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        Domain\AdminUser\AdminUserRepository::class => EloquentRepository\AdminUserRepository::class,
        Domain\Drug\DrugRepository::class => EloquentRepository\DrugRepository::class,
        Domain\MedicationHistory\MedicationHistoryRepository::class => EloquentRepository\MedicationHistoryRepository::class,
        Domain\Channel\ChannelRepository::class => EloquentRepository\ChannelRepository::class,
        Domain\Message\MessageRepository::class => EloquentRepository\MessageRepository::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
