<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\AdminLte;

class AdminLteServiceProvider extends ServiceProvider
{
    public function boot(Factory $factory): void
    {
        $factory->composer('vendor.adminlte.page', function (\Illuminate\View\View $v) {
            $v->with('adminlte', $this->app->make(AdminLte::class));
        });
    }
}
