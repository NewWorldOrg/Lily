<?php

namespace App\Http\Api\Common\Responder;

use Domain\Common\ListValue;
use Illuminate\Http\JsonResponse;

class ApiErrorResponder extends BaseResponder
{
    private ListValue $response;

    public function __construct(string $key)
    {
        $errors = config('api_errors');
        if (!isset($errors[$key])) {
            $this->response = new ListValue([
                'body' => [
                    'status' => false,
                    'message' => 'undefined error @ /config/api_errors.php',
                    'errors' => [
                        'type' => 'unknown_error',
                    ],
                    'data' => null,
                ],
                'response_code' => 400,
            ]);
            return;
        }
        $this->response = new ListValue([]);
        $this->response['body'] = [
            'status' => $errors[$key]['status'] ?? false,
            'message' => $errors[$key]['message'] ?? '',
            'errors' => isset($errors[$key]['type']) ? ['type' => $errors[$key]['type']] : [],
            'data' => null,
        ];

        $this->response['response_code'] = $errors[$key]['response_code'] ?? 500;
    }

    public function toResponse($request): JsonResponse
    {
        return new JsonResponse($this->response['body'], $this->response['response_code']);
    }
}
