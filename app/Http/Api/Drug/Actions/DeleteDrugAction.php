<?php


declare(strict_types=1);

namespace App\Http\Api\Drug\Actions;

use App\Http\Api\Common\Responder\ApiErrorResponder;
use App\Http\Api\Common\Responder\EmptyResponder;
use App\Http\Controllers\Controller;
use App\Services\DrugService;
use Domain\Drug\DrugId;
use OpenApi\Attributes\Delete;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

#[Delete(
    path: '/api/drugs/{id}',
    summary: '薬を削除する',
    tags: ['Drug'],
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
class DeleteDrugAction extends Controller
{
    public function __construct(private DrugService $drugService)
    {
        parent::__construct();
    }

    public function __invoke(int $id): EmptyResponder|ApiErrorResponder
    {
        $result = $this->drugService->delete(new DrugId($id));

        if ($result->isFailed()) {
            return new ApiErrorResponder($result->getError());
        }

        return new EmptyResponder();
    }
}
