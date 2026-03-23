<?php

declare(strict_types=1);

namespace Domain\MedicationHistory;

class MedicationNote
{
    public function __construct(private ?string $value)
    {
    }

    public function getRawValue(): ?string
    {
        return $this->value;
    }
}
