<?php

declare(strict_types=1);

namespace Domain\AdminUser;

use Domain\Base\BaseEnum;
use Domain\Common\ListValue;
use Domain\Common\RawInteger;
use Domain\Common\RawString;

enum AdminUserRole: int implements BaseEnum
{
    case ROLE_SYSTEM = 1;
    case ROLE_OPERATOR = 2;

    public function displayName(): RawString
    {
        return match($this) {
            self::ROLE_SYSTEM => new RawString('システム管理者'),
            self::ROLE_OPERATOR => new RawString('管理者'),
        };
    }

    public static function displayNameList(): ListValue
    {
        return new ListValue([
            self::ROLE_SYSTEM->getValue()->getRawValue() => new RawString('システム管理者'),
            self::ROLE_OPERATOR->getValue()->getRawValue() => new RawString('管理者'),
        ]);
    }

    public function isSystem(): bool
    {
        return match($this) {
            self::ROLE_SYSTEM => true,
            self::ROLE_OPERATOR => false,
        };
    }

    public function isOperator(): bool
    {
        return match($this) {
            self::ROLE_SYSTEM => false,
            self::ROLE_OPERATOR => true,
        };
    }

    public function getValue(): RawInteger
    {
        return new RawInteger($this->value);
    }
}
