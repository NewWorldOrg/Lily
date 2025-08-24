<?php

declare(strict_types=1);

namespace Tests\DbIntegration\Domain\MedicationHistory;

use Domain\Drug\DrugId;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationDate;
use Domain\MedicationHistory\MedicationHistoryDomainService;
use Domain\MedicationHistory\UserId;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\DbIntegration\DbIntegrationTestCase;

class MedicationHistoryDomainServiceTest extends DbIntegrationTestCase
{
    private MedicationHistoryDomainService $medicationHistoryDomainService;

    public function setUp(): void
    {
        parent::setUp();

        $this->medicationHistoryDomainService = $this->app->make(MedicationHistoryDomainService::class);
    }

    #[DataProvider('provideCreate')]
    public function testCreate(UserId $userId, DrugId $drugId, Amount $amount, ?MedicationDate $medicationDate): void
    {
        $result = $this->medicationHistoryDomainService->create($userId, $drugId, $amount, $medicationDate);

        $this->assertTrue($userId->isEqual($result->getUserId()));
        $this->assertTrue($drugId->isEqual($result->getDrugId()));
        $this->assertTrue($amount->isEqual($result->getAmount()));

        if (is_null($medicationDate)) {
            $this->assertTrue($result->getCreatedAt()->isEqual($result->getMedicationDate()));
            return;
        }

        $this->assertTrue($result->getMedicationDate()->isEqual($medicationDate));
    }

    public static function provideCreate(): array
    {
        return [
            [
                new UserId(930316),
                new DrugId(1),
                new Amount(2),
                MedicationDate::forStringTime('2025-03-16 03:16:05')
            ], [
                new UserId(930316),
                new DrugId(1),
                new Amount(0.5),
                null,
            ]
        ];
    }
}
