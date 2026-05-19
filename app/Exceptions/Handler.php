<?php

namespace App\Exceptions;

use App\Concerns\Responsable;
use GuzzleHttp\Psr7\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use PDOException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Handler extends ExceptionHandler
{
    use Responsable;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    protected function context(): array
    {
        return array_merge(parent::context(), [
            'RequestID' => request()->headers->get('X-Request-ID'),
        ]);
    }

    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse|Response
    {
        return $this->unauthorized($exception);
    }

    public function register()
    {
        $this->renderable(fn (PDOException $e) => $this->pdoException($e));
        $this->renderable(fn (AccessDeniedHttpException $e) => $this->forbidden($e));
    }
}
