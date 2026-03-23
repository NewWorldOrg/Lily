<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Responders;

use App\Http\Api\Common\Responder\BaseResponder;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'update_drug_responder',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class UpdateDrugResponder extends BaseResponder
{
    public function __construct()
    {
    }
}
