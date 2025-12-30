<?php

declare(strict_types=1);

namespace App\Http\Api\MedicationHistory\Actions;

use App\Http\Api\MedicationHistory\Requests\GetMedicationHistoryListRequest;
use App\Http\Api\MedicationHistory\Responders\GetMedicationHistoryListResponder;
use App\Http\Controllers\Controller;
use App\Services\MedicationHistoryService;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;


#[Get(
    path: '/medication_histories',
    summary: '服薬履歴を一覧で取得する',
    tags: ['Drug'],
    parameters: [
        new Parameter(
            name: 'page',
            description: 'ページ番号',
            in: 'query',
            required: false,
            schema: new Schema(type: 'integer'),
        ),
        new Parameter(
            name: 'per_page',
            description: '各ページで返す個数',
            in: 'query',
            required: false,
            schema: new Schema(type: 'integer'),
        ),
        new Parameter(
            name: 'user_id',
            description: 'DiscordのUserId',
            in: 'query',
            required: true,
            schema: new Schema(type: 'integer'),
        )
    ],
    responses: [
        new Response(
            response: 200,
            description: 'success',
            content: new JsonContent(
                ref: '#/components/schemas/get_drug_list_responder',
            )
        )
    ]
)]
class GetMedicationHistoryListAction extends Controller
{
    public function __construct(private MedicationHistoryService $medicationHistoryService)
    {
        parent::__construct();
    }

    public function __invoke(GetMedicationHistoryListRequest $request): GetMedicationHistoryListResponder
    {
        $result = $this->medicationHistoryService->getMedicationHistoryList(
            $request->makePaginate(),
            $request->getUserId()
        );

        return new GetMedicationHistoryListResponder($result->getData());
    }
}
