<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

class DataBaseQueryLoggerServiceProvider extends ServiceProvider
{
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
        if (in_array($this->app->environment(),['local', 'testing']) || $this->app->runningInConsole()) {
            return;
        }

        $logChannel = $this->app->environment('local')
            ? 'sql'
            : 'stack';

        DB::listen(function ($query) use ($logChannel) {
            $route =  'NOT URL';

            if (!is_null(Request::route())) {
                $route = Request::route()->getName();
            }

            $sql = $query->sql;
            foreach ($query->bindings as $binding) {
                if (is_string($binding)) {
                    $binding = "'{$binding}'";
                } elseif (is_bool($binding)) {
                    $binding = $binding ? '1' : '0';
                } elseif (is_int($binding)) {
                    $binding = (string) $binding;
                } elseif ($binding === null) {
                    $binding = 'NULL';
                } elseif ($binding instanceof Carbon) {
                    $binding = "'{$binding->timezone(config('app.timezone'))->toDateTimeString()}'";
                } elseif ($binding instanceof DateTime) {
                    $binding = "'{$binding->setTimezone(new DateTimeZone(config('app.timezone')))->format('Y-m-d H:i:s')}'";
                }
                $sql = preg_replace('/\\?/', $binding, $sql, 1);
            }
            Log::info($sql);
            Log::channel($logChannel)->info('[ROUTE]' . $route);
            Log::channel($logChannel)->info('[QUERY]' . $sql);
            Log::channel($logChannel)->info('[TIME]' . "time(ms):{$query->time}" . PHP_EOL);
        });
    }
}
