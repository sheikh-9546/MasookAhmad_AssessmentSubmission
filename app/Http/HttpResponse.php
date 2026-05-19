<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HttpResponse
{
    public function __construct(private $data, private string $message)
    {
        // TODO
    }

    public static function make(): static
    {
        return new static([], 'Ok');
    }

    public function setData($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function setMessage($message): static
    {
        $this->message = $message;

        return $this;
    }

    public function ok($code = Response::HTTP_OK): JsonResponse
    {
        return response()
            ->json(['message' => $this->message, 'data' => $this->data])
            ->setStatusCode($code)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return response()
            ->json(['message' => $this->message, 'data' => $this->data])
            ->setStatusCode($code)
            ->setEncodingOptions(JSON_UNESCAPED_SLASHES);

    }
}
