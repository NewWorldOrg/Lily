<?php

namespace App\Http\Api\Drug\Responders;

use App\DataTransfer\Drug\DrugListResult;
use App\Http\Api\Common\Responder\BaseResponder;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'get_drug_list_responder',
    allOf: [new Schema('#/components/schemas/base_responder')]
)]
class DrugListResponder extends BaseResponder
{
    #[Property(
        property: 'data',
        properties: [
            new Property(property: 'drugs', ref: '#/components/schemas/drug_list_result', type: 'object')
        ]
    )]
    public array $drugs;

    public function __construct(DrugListResult $drugListResult)
    {
        $this->drugs = $drugListResult->toArray();
    }
}
