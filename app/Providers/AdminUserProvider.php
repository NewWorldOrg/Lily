<?php

declare(strict_types=1);

namespace App\Providers;

use App\Auth\AdminUser;
use Domain\AdminUser\AdminId;
use Domain\AdminUser\AdminUserId;
use Domain\AdminUser\AdminUserRepository;
use Domain\Common\RawPassword;
use Domain\Exception\NotFoundException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class AdminUserProvider implements UserProvider
{
    public function __construct(
        private AdminUserRepository $adminUserRepository,
        private Hasher $hasher,
    ) {
    }

    public function retrieveByCredentials(array $credentials): AdminUser|null
    {
        if (!isset($credentials['user_id'])) {
            return null;
        }

        try {
            $adminUserDomain = $this->adminUserRepository->getByUserId(
                new AdminUserId($credentials['user_id']),
            );

            return new AdminUser($adminUserDomain);
        } catch (NotFoundException $e) {
            return null;
        }
    }

    public function retrieveById($identifier)
    {
        return new AdminUser($this->adminUserRepository->get(new AdminId($identifier)));
    }

    /**
     * @return AdminUserRepository
     */
    public function getAdminUserRepository(): AdminUserRepository
    {
        return $this->adminUserRepository;
    }

    /**
     * @param AdminUserRepository $adminUserRepository
     */
    public function setAdminUserRepository(AdminUserRepository $adminUserRepository): void
    {
        $this->adminUserRepository = $adminUserRepository;
    }

    public function retrieveByToken($identifier, $token)
    {
        // do noting
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        // do noting
    }

    public function validateCredentials(Authenticatable $user, array $credentials): bool
    {
        return $user->checkPassword($this->hasher, new RawPassword($credentials['password']));
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        // do noting
    }
}
