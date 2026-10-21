<?php

declare(strict_types=1);

namespace Domain\AdminUser;

use Domain\Common\HashedPassword;

class AdminUser
{
    public function __construct(
        private AdminId $adminId,
        private AdminUserId $adminUserId,
        private HashedPassword $password,
        private AdminUserName $adminUserName,
        private AdminUserRole $adminUserRole,
        private AdminUserStatus $adminUserStatus
    ) {
    }

    public function getId(): AdminId
    {
        return $this->adminId;
    }

    public function getUserId(): AdminUserId
    {
        return $this->adminUserId;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }

    public function getName(): AdminUserName
    {
        return $this->adminUserName;
    }

    public function getRole(): AdminUserRole
    {
        return $this->adminUserRole;
    }

    public function getStatus(): AdminUserStatus
    {
        return $this->adminUserStatus;
    }

    public function hasHashedPassword(): bool
    {
        return !is_null($this->password);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->adminId->getRawValue(),
            'user_id' => $this->adminUserId->getRawValue(),
            'password' => $this->password->getRawValue(),
            'name' => $this->adminUserName->getRawValue(),
            'role' => $this->adminUserRole->getValue()->getRawValue(),
            'status' => $this->adminUserStatus->getValue()->getRawValue(),
        ];
    }
}
