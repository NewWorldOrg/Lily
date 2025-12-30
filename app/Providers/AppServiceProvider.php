<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // 管理画面用のクッキー
        if ($this->isNotApiPath()) {
            config(['session.cookie' => config('session.cookie_admin')]);
        }

        Paginator::useBootstrap();

        if ($this->app->environment() === 'production') {
            \URL::forceScheme('https');
        }
    }

    protected function isNotApiPath(): bool
    {
        return request()->is('admin*') ||
            request()->is('api/documentation') ||
            request()->is('docs*');
    }
}
