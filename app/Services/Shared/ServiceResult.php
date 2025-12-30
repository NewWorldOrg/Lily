<?php

declare(strict_types=1);

namespace App\Services\Shared;

use Domain\Exception\ErrorMessage;

/**
 * @template T
 */
class ServiceResult
{
    /**
     * @template T
     * @param bool $status
     * @param ServiceError|null $error
     * @param T|null $data
     */
    public function __construct(
        private readonly bool $status,
        private readonly ?ServiceError $error,
        private readonly mixed $data,
    ) {
    }

    public function getStatus(): bool
    {
        return $this->status;
    }

    public function getError(): ?string
    {
        return $this->error->value;
    }

    /**
     * @return T|null
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    public function isFailed(): bool
    {
        return !$this->status;
    }

    /**
     * @param T $data
     */
    public static function success(mixed $data): ServiceResult
    {
        return new ServiceResult(true, null, $data);
    }

    public static function fail(ServiceError $error ): ServiceResult
    {
        return new ServiceResult(false, $error, null);
    }
}
