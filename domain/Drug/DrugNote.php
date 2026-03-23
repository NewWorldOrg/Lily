<?php

declare(strict_types=1);

namespace Domain\Drug;

class DrugNote
{
    public function __construct(private ?string $value)
    {
    }

    public function getRawValue(): ?string
    {
        return $this->value;
    }
}
