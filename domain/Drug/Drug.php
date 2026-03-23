<?php

declare(strict_types=1);

namespace Domain\Drug;

class Drug
{
    private DrugId $drugId;
    private DrugName $drugName;
    private DrugUrl $drugUrl;
    private DrugNote $drugNote;

    public function __construct(DrugId $drugId, DrugName $drugName, DrugUrl $drugUrl, DrugNote $drugNote)
    {
        $this->drugId = $drugId;
        $this->drugName = $drugName;
        $this->drugUrl = $drugUrl;
        $this->drugNote = $drugNote;
    }

    public function getId(): DrugId
    {
        return $this->drugId;
    }

    public function getName(): DrugName
    {
        return $this->drugName;
    }

    public function getUrl(): DrugUrl
    {
        return $this->drugUrl;
    }

    public function getNote(): DrugNote
    {
        return $this->drugNote;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getRawValue(),
            'drugName' => $this->getName()->getRawValue(),
            'url' => $this->getUrl()->getRawValue(),
            'note' => $this->getNote()->getRawValue(),
        ];
    }
}
