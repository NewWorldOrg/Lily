<?php

declare(strict_types=1);

namespace App\Http\Api\Common\Responder;

use OpenApi\Attributes\Schema;

#[Schema(
    schema: '200_empty',
    required: ['status', 'message', 'errors', 'data'],
    type: 'object',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class EmptyResponder extends BaseResponder
{
}
