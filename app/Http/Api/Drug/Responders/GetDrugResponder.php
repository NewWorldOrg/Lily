<?php

declare(strict_types=1);

namespace App\Http\Api\Drug\Responders;

use App\DataTransfer\Drug\DrugDetail;
use App\Http\Api\Common\Responder\BaseResponder;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'get_drug_responder',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class GetDrugResponder extends BaseResponder
{
    #[Property(
        property: 'data',
        properties: [
            new Property(
                property: 'drug',
                ref: '#/components/schemas/drug_detail',
                type: 'object',
            )
        ],
    )]
    public function __construct(public readonly DrugDetail $drug)
    {
    }
}
