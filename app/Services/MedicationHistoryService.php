<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransfer\MedicationHistory\MedicationHistoryDetail;
use App\DataTransfer\MedicationHistory\MedicationHistoryDetailList;
use App\DataTransfer\MedicationHistory\MedicationHistoryListResult;
use App\Services\Shared\ServiceError;
use App\Services\Shared\ServiceResult;
use Domain\Common\Paginator\Paginate;
use Domain\Drug\DrugDomainService;
use Domain\Drug\DrugHashMap;
use Domain\Drug\DrugId;
use Domain\Drug\DrugName;
use Domain\Exception\NotFoundException;
use Domain\MedicationHistory\MedicationDate;
use Domain\MedicationHistory\MedicationHistory;
use Domain\MedicationHistory\Amount;
use Domain\MedicationHistory\MedicationHistoryDomainService;
use Domain\MedicationHistory\MedicationHistoryId;
use Domain\MedicationHistory\MedicationHistoryRepository;
use Domain\MedicationHistory\UserId;
use App\Services\Service as AppService;

class MedicationHistoryService extends AppService
{
    public function __construct(
        private MedicationHistoryDomainService $medicationHistoryDomainService,
        private DrugDomainService $drugDomainService,
        private MedicationHistoryRepository $medicationHistoryRepository,
    ) {
    }

    /**
     * @param MedicationHistoryId $id
     * @return ServiceResult<MedicationHistoryDetail>
     */
    public function getDetail(MedicationHistoryId $id): ServiceResult
    {
        try {
            $medicationHistory = $this->medicationHistoryRepository->get($id);
            $drug = $this->drugDomainService->get($medicationHistory->getDrugId());

            $medicationHistoryDetail = new MedicationHistoryDetail($medicationHistory, $drug);

            return ServiceResult::success($medicationHistoryDetail);
        } catch (NotFoundException) {
            return ServiceResult::fail(ServiceError::NotFound);
        }
    }

    public function getMedicationHistoryList(Paginate $paginate, UserId $userId): ServiceResult
    {
        $result = $this->medicationHistoryRepository->getPaginatorByUserId($paginate, $userId);
        $drugList = $this->drugDomainService->getDrugList();
        $drugHashMap = new DrugHashMap($drugList);
        $medicationHistoryDetailList = new MedicationHistoryDetailList([]);

        foreach ($result as $key => $item) {
            /** @var MedicationHistory $item */
            $drug = $drugHashMap->get((string)$item->getDrugId());
            $medicationHistoryDetailList[$key] = new MedicationHistoryDetail($item, $drug);
        }

        $paginator = new MedicationHistoryListResult(
            $medicationHistoryDetailList,
            $this->medicationHistoryRepository->getCount(),
            $paginate,
        );

        return ServiceResult::success($paginator);
    }

    /**
     * Get all medication history
     *
     * @param Paginate $paginate
     * @return MedicationHistoryListResult
     */
    public function getMedicationHistoryPaginator(Paginate $paginate): MedicationHistoryListResult
    {
        $result = $this->medicationHistoryDomainService->getPaginate($paginate);

        $medicationHistoryDetailList = new MedicationHistoryDetailList([]);

        foreach ($result as $key => $item) {
            /** @var MedicationHistory $item */
            $drug = $this->drugDomainService->get($item->getDrugId());
            $medicationHistoryDetailList[$key] = new MedicationHistoryDetail($item, $drug);
        }

        return new MedicationHistoryListResult(
            $medicationHistoryDetailList,
            $this->medicationHistoryRepository->getCount(),
            $paginate,
        );
    }

    /**
     * Update medication history
     *
     * @param MedicationHistory $medicationHistory
     * @return MedicationHistory
     */
    public function updateMedicationHistory(MedicationHistory $medicationHistory): MedicationHistory
    {
        return $this->medicationHistoryDomainService->update($medicationHistory);
    }

    /**
     * @param DrugId $drugId
     * @param UserId $userId
     * @param Amount $amount
     * @param MedicationDate $medicationDate
     * @return ServiceResult<MedicationHistoryDetail>
     */
    public function createMedicationHistory(
        DrugId $drugId,
        UserId $userId,
        Amount $amount,
        MedicationDate $medicationDate,
    ): ServiceResult {
        $medicationHistory = $this->medicationHistoryDomainService->create(
            $userId,
            $drugId,
            $amount,
            $medicationDate,
        );
        $drug = $this->drugDomainService->get($medicationHistory->getDrugId());

        $result = new MedicationHistoryDetail($medicationHistory, $drug);

        return ServiceResult::success($result);
    }
}
