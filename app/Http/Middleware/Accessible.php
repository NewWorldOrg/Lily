<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Auth\AdminUser;
use Closure;
use Domain\AdminUser\AdminUserRole;
use Illuminate\Support\Str;

class Accessible
{

    public function handle($request, Closure $next, $guards = null) {
        /** @var AdminUser $currentUser */
        $currentUser = \Auth::user();

        // Current route is not one of available routes
        if ($currentUser) {
            // Current route is not one of available routes
            $canAccess = $this->canAccess($currentUser, \Route::currentRouteName());

            abort_unless($canAccess, 403);
        }

        return $next($request);
    }

    public function canAccess(AdminUser $adminUser, ?string $routeName): bool
    {
        if (is_null($routeName)) {
            return false;
        }

        $accessibleRoutes = $this->getAccessibleRoutes(
            $adminUser->getAdminUser()->getRole()
        );

        return $this->containsCurrentRoute($routeName, $accessibleRoutes);
    }

    /**
     *
     *
     * @param int $roleId
     * @return array
     */
    protected function getAccessibleRoutes(AdminUserRole $adminUserRole): array
    {

        $routes = [
            AdminUserRole::ROLE_SYSTEM->getValue()->getRawValue() => [
                'admin.auth.*',
                'admin.admin_users.*',
                'admin.top_page',
                'admin.drugs.*',
                'admin.medication_histories.*',
                'l5-swagger.*',
            ],
            AdminUserRole::ROLE_OPERATOR->getValue()->getRawValue() => [
                'admin.auth.*',
                'admin.top_page',
                'admin.drugs.*',
                'admin.medication_histories.*',
            ],
        ];

        return (array)data_get($routes, $adminUserRole->getValue()->getRawValue(), []);

    }

    /**
     *
     *
     * @param array $availableRoutes
     * @return bool
     */
    protected function containsCurrentRoute(string $routeName, array $availableRoutes): bool
    {
        foreach ($availableRoutes as $route) {
            if (Str::is($route, $routeName)) {
                return true;
            }
        }

        return false;
    }

}
