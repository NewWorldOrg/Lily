<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Actions;

use App\Http\Api\Drug\Requests\GetDrugListRequest;
use App\Http\Api\Drug\Responders\DrugListResponder;
use App\Http\Controllers\Controller;
use App\Services\DrugService;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

#[Get(
    path: '/api/drugs',
    summary: '薬を一覧で取得する',
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
class GetDrugListAction extends Controller
{
    public function __construct(private DrugService $drugService)
    {
        parent::__construct();
    }

    public function __invoke(GetDrugListRequest $request): DrugListResponder
    {
        $result = $this->drugService->getDrugList($request->makePaginate());

        return new DrugListResponder($result->getData());
    }
}
