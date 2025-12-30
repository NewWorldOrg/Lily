<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Actions;

use App\Http\Api\Common\Responder\ApiErrorResponder;
use App\Http\Api\Drug\Responders\GetDrugResponder;
use App\Http\Controllers\Controller;
use App\Services\DrugService;
use Domain\Drug\DrugId;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Post;
use OpenApi\Attributes\RequestBody;
use OpenApi\Attributes\Response;

#[Post(
    path: '/drugs',
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
class GetDrugAction extends Controller
{
    public function __construct(private DrugService $drugService)
    {
        parent::__construct();
    }

    public function __invoke(int $id): GetDrugResponder|ApiErrorResponder
    {
        $result = $this->drugService->getDrug(new DrugId($id));

        if ($result->isFailed()) {
            return new ApiErrorResponder($result->getError());
        }

        return new GetDrugResponder($result->getData());
    }
}
