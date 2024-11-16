<?php

declare(strict_types=1);

namespace App\Auth;

use Domain\AdminUser\AdminUser as AdminUserDomain;
use Domain\Common\RawPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Hashing\Hasher;

class AdminUser implements Authenticatable
{
    public function __construct(
       private AdminUserDomain $adminUser,
    ) {
    }

    /**
     * @return AdminUserDomain
     */
    public function getAdminUser(): AdminUserDomain
    {
        return $this->adminUser;
    }

    /**
     * @param AdminUserDomain $adminUser
     */
    public function setAdminUser(AdminUserDomain $adminUser): void
    {
        $this->adminUser = $adminUser;
    }

    /**
     * NOTE: admin-lteがnameを呼ぶので対処
     */
    public function __get(string $name)
    {
        if ($name = 'name') {
            return $this->getAdminUser()->getName()->getRawValue();
        }
    }

    public function getAuthIdentifier(): int
    {
        return $this->adminUser->getId()->getRawValue();
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthPassword(): string
    {
        return $this->adminUser->getPassword()->getRawValue();
    }

    public function getRememberToken()
    {
        // do noting
    }

    public function getRememberTokenName()
    {
        // do noting
    }
    public function setRememberToken($value)
    {
        // do noting
    }

    public function getAuthPasswordName()
    {
        // do noting
    }

    public function checkPassword(Hasher $hasher, RawPassword $rawPassword): bool
    {
        if (!$this->adminUser->hasHashedPassword()) {
            return false;
        }

        return $this->adminUser->getPassword()->check($hasher, $rawPassword);
    }
}
