<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Actions;

use App\Http\Api\Drug\Requests\UpdateDrugRequest;
use App\Http\Api\Drug\Responders\UpdateDrugResponder;
use App\Http\Controllers\Controller;
use App\Services\DrugService;
use Domain\Drug\DrugId;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\PathParameter;
use OpenApi\Attributes\Put;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

#[Put(
    path: '/api/drugs/{id}',
    summary: '薬の更新',
    requestBody: new RequestBody(
        content: new JsonContent(
            ref: '#/components/schemas/update_drug_request'
        )
    ),
    tags: ['Drug'],
    parameters: [
        new PathParameter(name: 'id', required: true, schema: new \OpenApi\Attributes\Schema(type: 'integer')),
    ],
    responses: [
        new Response(
            response: 200,
            description: 'Successful',
            content: new JsonContent(
                ref: '#/components/schemas/update_drug_responder'
            )
        ),
        new Response(
            response: 401,
            description: 'Unauthorized',
        )
    ]
)]
class UpdateDrugAction extends Controller
{
    public function __construct(private DrugService $drugService)
    {
        parent::__construct();
    }

    public function __invoke(int $id, UpdateDrugRequest $request): UpdateDrugResponder
    {
        $this->drugService->updateDrug(
            new DrugId($id),
            $request->getDrugName(),
            $request->getUrl(),
            $request->getNote(),
        );

        return new UpdateDrugResponder();
    }
}
