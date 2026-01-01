<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransfer\Drug\DrugDetail;
use App\DataTransfer\Drug\DrugListResult;
use App\DataTransfer\Drug\DrugPaginator;
use App\Services\Service as AppService;
use App\Services\Shared\ServiceError;
use App\Services\Shared\ServiceResult;
use Domain\Common\Paginator\Paginate;
use Domain\Common\RawPositiveInteger;
use Domain\Drug\Drug;
use Domain\Drug\DrugDomainService;
use Domain\Drug\DrugId;
use Domain\Drug\DrugName;
use Domain\Drug\DrugRepository;
use Domain\Drug\DrugUrl;
use Domain\Exception\NotFoundException;
use Domain\MedicationHistory\MedicationHistoryDomainService;
use Infra\EloquentModels\Drug as DrugModel;

class DrugService extends AppService
{
    public function __construct(
        private DrugDomainService $drugDomainService,
        private MedicationHistoryDomainService $medicationHistoryDomainService,
        private DrugRepository $drugRepository,
    ) {
    }

    public function get(DrugId $id): Drug
    {
        return $this->drugRepository->get($id);
    }

    /**
     * Paginate Drugs
     *
     * @param Paginate $paginate
     * @return DrugPaginator
     */
    public function getDrugsPaginator(Paginate $paginate): DrugPaginator
    {
        $drugList = $this->drugDomainService->getPaginator($paginate);

        return new DrugPaginator(
            $drugList,
            $this->drugRepository->getCount(),
            $paginate,
        );
    }

    /**
     * @param Paginate $paginate
     * @return ServiceResult<DrugListResult>
     */
    public function getDrugList(Paginate $paginate): ServiceResult
    {
        $drugList = $this->drugDomainService->getPaginator($paginate);

        $result = new DrugListResult(
            $drugList,
            $this->drugRepository->getCount(),
            $paginate,
        );

        return ServiceResult::success($result);
    }

    /**
     * Find a drug
     *
     * @param DrugId $drugId
     * @return ServiceResult<DrugDetail>
     */
    public function getDrug(DrugId $drugId): ServiceResult
    {
        try {
            $drug = $this->drugDomainService->get($drugId);
            $drugDetail = new DrugDetail($drug);

            return ServiceResult::success($drugDetail);
        } catch (NotFoundException) {
            return ServiceResult::fail(ServiceError::NotFound);
        }
    }

    /**
     * Find drug by name
     *
     * @param DrugName $drugName
     * @return Drug
     */
    public function searchDrugByName(DrugName $drugName): array
    {
        try {
            $drug = $this->drugDomainService->findDrugByName($drugName);

            return [
                'status' => true,
                'errors' => null,
                'data' => $drug->toArray(),
            ];
        } catch (NotFoundException) {
            return [
                'status' => false,
                'errors' => [
                    'key' => 'drug_notfound',
                ],
                'data' => null
            ];
        }
    }

    /**
     * Create a drug
     *
     * @param DrugName $drugName
     * @param DrugUrl $url
     * @return ServiceResult<DrugDetail>
     */
    public function createDrug(DrugName $drugName, DrugUrl $url): ServiceResult
    {
        $drug = $this->drugDomainService->createDrug(
            $drugName,
            $url,
        );

      return ServiceResult::success(new DrugDetail($drug));
    }

    /**
     * Update a drug
     *
     * @param DrugId $drugId
     * @param DrugName $drugName
     * @param DrugUrl $drugUrl
     * @return array
     */
    public function updateDrug(
        DrugId $drugId,
        DrugName $drugName,
        DrugUrl $drugUrl,
    ): array {

        $result = $this->drugDomainService->updateDrug(
            new Drug(
                $drugId,
                $drugName,
                $drugUrl,
            )
        );

        if (!$result) {
            return [
                'status' => false,
                'message' => 'Failed update drug',
                'errors' => [
                    'key' => 'failed_update_drug',
                ],
                'data' => null,
            ];
        }

        return [
            'status' => true,
            'message' => '',
            'errors' => null,
            'data' => null,
        ];
    }

    /**
     * Delete the drug
     *
     * @param DrugModel $drug
     * @return array
     */
    public function deleteDrug(DrugId $drugId): array
    {
        try {
            $medicationHistories = $this->medicationHistoryDomainService->getCountMedicationTake(
                $drugId,
            );

            if ($medicationHistories->isLessThan(RawPositiveInteger::makeZero())) {
                return [
                    'status' => false,
                    'message' => 'Have a medication history',
                    'errors' => [
                        'key' => 'have_a_medication_history',
                    ],
                    'data' => null,
                ];
            }

            $this->drugDomainService->deleteDrug($drugId);

            return [
                'status' => true,
                'message' => '',
                'errors' => null,
                'data' => null,
            ];
        } catch (NotFoundException $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete',
                'errors' => [
                    'key' => 'failed_to_delete',
                ],
                'data' => null,
            ];
        }
    }

    /**
     * @param DrugId $id
     * @return ServiceResult<null>
     */
    public function delete(DrugId $id): ServiceResult
    {
        try {
            $this->drugDomainService->deleteDrug($id);

            return ServiceResult::success(null);
        } catch (NotFoundException) {
            return ServiceResult::fail(ServiceError::NotFound);
        }
    }
}
