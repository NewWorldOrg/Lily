<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Actions;

use App\Http\Api\MedicationHistory\Requests\UpdateMedicationHistoryRequest;
use App\Http\Api\MedicationHistory\Responders\UpdateMedicationHistoryResponder;
use App\Http\Controllers\Controller;
use App\Services\MedicationHistoryService;
use Domain\Common\UpdatedAt;
use Domain\Drug\DrugDomainService;
use Domain\MedicationHistory\MedicationHistory;
use Domain\MedicationHistory\MedicationHistoryId;
use Domain\MedicationHistory\MedicationHistoryRepository;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

#[Put(
    path: '/api/medication_histories/{id}',
    summary: '服薬履歴の更新',
    requestBody: new RequestBody(
        content: new JsonContent(
            ref: '#/components/schemas/update_medication_history_request'
        )
    ),
    tags: ['MedicationHistory'],
    parameters: [
        new PathParameter(name: 'id', required: true, schema: new \OpenApi\Attributes\Schema(type: 'integer')),
    ],
    responses: [
        new Response(
            response: 200,
            description: 'Successful',
            content: new JsonContent(
                ref: '#/components/schemas/update_medication_history_responder'
            )
        ),
        new Response(
            response: 401,
            description: 'Unauthorized',
        )
    ]
)]
class UpdateMedicationHistoryAction extends Controller
{
    public function __construct(
        private MedicationHistoryService $medicationHistoryService,
        private MedicationHistoryRepository $medicationHistoryRepository,
        private DrugDomainService $drugDomainService,
    ) {
        parent::__construct();
    }

    public function __invoke(int $id, UpdateMedicationHistoryRequest $request): UpdateMedicationHistoryResponder
    {
        $existing = $this->medicationHistoryRepository->get(new MedicationHistoryId($id));

        $medicationHistory = new MedicationHistory(
            $existing->getId(),
            $existing->getUserId(),
            $existing->getDrugId(),
            $request->getAmount(),
            $existing->getMedicationDate(),
            $request->getNote(),
            $existing->getCreatedAt(),
            UpdatedAt::now(),
        );

        $updated = $this->medicationHistoryService->updateMedicationHistory($medicationHistory);
        $drug = $this->drugDomainService->get($updated->getDrugId());

        $result = new \App\DataTransfer\MedicationHistory\MedicationHistoryDetail($updated, $drug);

        return new UpdateMedicationHistoryResponder($result);
    }
}
