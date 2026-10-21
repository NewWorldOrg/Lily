<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use Infra\EloquentModels\AdminUser;

class DrugPolicy
{
    public function create(AdminUser $adminUser): Response
    {
        $adminUserDomain = $adminUser->toDomain();

        return ($adminUserDomain->getRole()->isSystem() || $adminUserDomain->getRole()->isOperator())
            ? Response::allow()
            : Response::deny('You do not own system role.');
    }

    public function update(AdminUser $adminUser): Response
    {
        $adminUserDomain = $adminUser->toDomain();

        return ($adminUserDomain->getRole()->isSystem() || $adminUserDomain->getRole()->isOperator())
            ? Response::allow()
            : Response::deny('You do not own system role.');
    }
}
