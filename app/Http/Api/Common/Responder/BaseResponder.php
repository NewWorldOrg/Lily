<?php

declare(strict_types=1);

namespace App\Http\Api\Common\Responder;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Schema;

#[Schema(
    schema: 'base_responder',
    required: ['status', 'message', 'errors', 'data'],
    properties: [
        new Property(
            property: 'status',
            type: 'boolean',
            example: true,
        ),
        new Property(
            property: 'message',
            type: 'string',
            example: '高田憂希しか好きじゃない',
        ),
        new Property(
            property: 'errors',
            type: 'string',
            example: null,
        ),
        new Property(
            property: 'data',
            type: 'array',
            example: null,
        ),
    ],
    type: 'object'
)]
abstract class BaseResponder implements Responsable
{
    protected const STATUS_CODE = 200;

    public function toResponse($request): JsonResponse
    {
        $result = [
            'status' => true,
            'message' => '',
            'errors' => null,
        ];

        $result['data'] = $this->objectToArray($this) ?: null;
        return new JsonResponse($result, static::STATUS_CODE);
    }

    protected function objectToArray($data, bool $strict = true)
    {
        if (true === \is_object($data)) {
            if (method_exists($data, '__toArray')) {
                return $data->__toArray($strict);
            }

            if (method_exists($data, '__toString')) {
                return $data->__toString();
            }

            $array = [];
            foreach ((array) $data as $key => $value) {
                $key = preg_replace('/\000(.*)\000/', '', $key);
                $array[$key] = $this->objectToArray($value, $strict);
            }

            return $array;
        }

        if (true === \is_array($data)) {
            $stack = [];
            foreach ($data as $key => $value) {
                $stack[$key] = $this->objectToArray($value, $strict);
            }

            return $stack;
        }

        return $data;
    }
}
