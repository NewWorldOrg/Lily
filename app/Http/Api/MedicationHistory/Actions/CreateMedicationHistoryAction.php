<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Actions;

use App\Http\Api\MedicationHistory\Requests\CreateMedicationHistoryRequest;
use App\Http\Api\MedicationHistory\Responders\CreateMedicationHistoryResponder;
use App\Http\Controllers\Controller;
use App\Services\MedicationHistoryService;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

#[Post(
    path: '/api/medication_histories',
    summary: '服薬履歴の作成',
    requestBody: new RequestBody(
        content: new JsonContent(
            ref: '#/components/schemas/create_medication_history_request'
        )
    ),
    tags: ['MedicationHistory'],
    responses: [
        new Response(
            response: 200,
            description: 'Successful',
            content: new JsonContent(
                ref: '#/components/schemas/create_medication_history_responder'
            )
        ),
        new Response(
            response: 403,
            description: 'Forbidden',
            content: new JsonContent(
                ref: '#/components/schemas/api_error_responder'
            )
        )
    ]
)]
class CreateMedicationHistoryAction extends Controller
{
    public function __construct(private MedicationHistoryService $medicationHistoryService)
    {
        parent::__construct();
    }

    public function __invoke(CreateMedicationHistoryRequest $request): CreateMedicationHistoryResponder
    {
        $result = $this->medicationHistoryService->createMedicationHistory(
            $request->getDrugId(),
            $request->getUserId(),
            $request->getAmount(),
            $request->getMedicationData(),
        );

        return new CreateMedicationHistoryResponder($result->getData());
    }
}
