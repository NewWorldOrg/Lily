<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Actions;

use App\Http\Api\Common\Responder\ApiErrorResponder;
use App\Http\Api\Common\Responder\EmptyResponder;
use App\Http\Controllers\Controller;
use App\Services\MedicationHistoryService;
use Domain\MedicationHistory\MedicationHistoryId;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

#[Delete(
    path: '/api/medication_histories/{id}',
    summary: '服薬履歴を削除する',
    tags: ['MedicationHistory'],
    parameters: [
        new Parameter(
            name: 'id',
            in: 'path',
            required: true,
            schema: new Schema(
                type: 'integer'
            )
        )
    ],
    responses: [
        new Response(
            response: '200',
            description: 'success',
            content: new JsonContent(
                ref: '#/components/schemas/base_responder'
            )
        ),
        new Response(
            response: '404',
            description: 'not found',
            content: new JsonContent(
                ref: '#/components/schemas/api_error_responder'
            )
        ),
    ]
)]
class DeleteMedicationHistoryAction extends Controller
{
    public function __construct(private MedicationHistoryService $medicationHistoryService)
    {
        parent::__construct();
    }

    public function __invoke(int $id): EmptyResponder|ApiErrorResponder
    {
        $result = $this->medicationHistoryService->delete(new MedicationHistoryId($id));

        if ($result->isFailed()) {
            return new ApiErrorResponder($result->getError());
        }

        return new EmptyResponder();
    }
}
