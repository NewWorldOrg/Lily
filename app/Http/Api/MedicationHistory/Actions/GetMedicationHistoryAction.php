<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Actions;

use App\Http\Api\Common\Responder\ApiErrorResponder;
use App\Http\Api\MedicationHistory\Responders\GetMedicationHistoryResponder;
use App\Http\Controllers\Controller;
use App\Services\MedicationHistoryService;
use Domain\MedicationHistory\MedicationHistoryId;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Response;

#[Get(
    path: '/medication_histories',
    summary: '服薬履歴の詳細',
    tags: ['MedicationHistory'],
    responses: [
        new Response(
            response: 200,
            description: 'Successful',
            content: new JsonContent(
                ref: '#/components/schemas/get_medication_history_responder',
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
class GetMedicationHistoryAction extends Controller
{
    public function __construct(private MedicationHistoryService $medicationHistoryService)
    {
        parent::__construct();
    }

    public function __invoke(int $id): GetMedicationHistoryResponder|ApiErrorResponder
    {
        $result = $this->medicationHistoryService->getDetail(new MedicationHistoryId($id));

        if ($result->isFailed()) {
            return new ApiErrorResponder($result->getError());
        }

        return new GetMedicationHistoryResponder($result->getData());
    }
}
