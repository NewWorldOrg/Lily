<?php

declare(strict_types=1);

namespace Domain\DiscordBot\CommandArgument;

use Domain\Drug\DrugName;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationDate;

class MedicationCommandArgument
{
    private DrugName $drugName;
    private Amount $amount;
    private ?MedicationDate $medicationDate;

    public function __construct(array $args)
    {
        $this->drugName = new DrugName($args[0]);
        $this->amount = new Amount((float)$args[1]);

        if (is_null($args[2])) {
            $this->medicationDate = MedicationDate::forStringTime((string)$args[2]);
        }
    }

    public function getDrugName(): DrugName
    {
        return $this->drugName;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getMedicationDate(): ?MedicationDate
    {
        return $this->medicationDate;
    }
}
