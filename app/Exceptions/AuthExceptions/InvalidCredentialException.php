<?php

namespace App\Exceptions\AuthExceptions;

use App\Concerns\Responsable;
use Exception;
use Illuminate\Http\JsonResponse;

class InvalidCredentialException extends Exception
{
    use Responsable;

    public function render($request): JsonResponse
    {
        return $this->badRequest();
    }
}
