<?php

namespace App\Concerns;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait Responsable
{
    protected function toResponseBuilder($message, $error, $description = null): array
    {
        return array_merge(
            ['message' => $message],
            app()->hasDebugModeEnabled()
                ? ['descriptions' => $description, 'errors' => $error]
                : ['descriptions' => null, 'errors' => null]
        );
    }

    protected function badRequest(): JsonResponse
    {
        return response()
            ->json($this->toResponseBuilder($this->getMessage(), $this->getTrace()))
            ->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    protected function pdoException($e): JsonResponse
    {
        return response()
            ->json($this->toResponseBuilder('Bad request', $e->getTrace(), $e->getMessage()))
            ->setStatusCode(Response::HTTP_BAD_REQUEST)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    protected function unauthorized(): JsonResponse
    {
        return response()
            ->json($this->toResponseBuilder('Unauthorized', null))
            ->setStatusCode(Response::HTTP_UNAUTHORIZED)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    protected function forbidden(AccessDeniedHttpException $e): JsonResponse
    {
        return response()
            ->json($this->toResponseBuilder(
                'You are forbidden to perform following action on resource.',
                $e->getMessage()
            ))
            ->setStatusCode(Response::HTTP_FORBIDDEN)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    protected function ok(): JsonResponse
    {
        return response()
            ->json(['message' => $this->getMessage(), 'data' => null])
            ->setStatusCode(Response::HTTP_OK)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }
}
