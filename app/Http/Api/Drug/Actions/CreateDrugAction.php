<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Actions;

use App\Http\Api\Drug\Requests\CreateDrugRequest;
use App\Http\Api\Drug\Responders\CreateDrugResponder;
use App\Http\Controllers\Controller;
use App\Services\DrugService;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

#[Post(
    path: '/api/drugs',
    summary: '薬の作成',
    requestBody: new RequestBody(
        content: new JsonContent(
            ref: '#/components/schemas/create_drug_request'
        )
    ),
    tags: ['Drug'],
    responses: [
        new Response(
            response: 200,
            description: 'Successful',
            content: new JsonContent(
                ref: '#/components/schemas/200_empty'
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
class CreateDrugAction extends Controller
{
    public function __construct(private DrugService $drugService)
    {
        parent::__construct();
    }

    public function __invoke(CreateDrugRequest $request): CreateDrugResponder
    {
        $result = $this->drugService->createDrug(
            $request->getDrugName(),
            $request->getUrl(),
        );

        return new CreateDrugResponder($result->getData());
    }
}
