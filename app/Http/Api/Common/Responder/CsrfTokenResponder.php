<?php

declare(strict_types=1);

namespace App\Http\Api\Common\Responder;

class CsrfTokenResponder extends BaseResponder
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}
