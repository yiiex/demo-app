<?php

namespace App\Http\Helpers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseHelper
{
    public function __construct(protected ResponseFactoryInterface $responseFactory)
    {

    }

    public function redirect($url, $status = 302): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse($status)
            ->withHeader('Location', $url)
            ->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Expires', '0');
    }

    public function json(
        $data,
        int $status = 200,
        int $jsonFlags = 0,
        array $headers = []
    ): ResponseInterface
    {
        $response = $this->responseFactory->createResponse($status);
        $body = $response->getBody();
        $body->write(json_encode($data, $jsonFlags));
        $response = $response->withHeader('Content-Type', 'application/json');
        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }
        $body->rewind();
        return $response->withStatus($status);
    }

    public function error(int $code, ?string $message = null): ResponseInterface
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        ];

        $message = $message ?? ($messages[$code] ?? 'Error');
        $response = $this->responseFactory->createResponse($code);
        $body = $response->getBody();
        $body->write($message);
        $body->rewind();
        return $response;
    }
}

