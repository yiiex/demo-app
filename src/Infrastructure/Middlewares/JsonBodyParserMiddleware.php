<?php

namespace App\Infrastructure\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonBodyParserMiddleware implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (str_contains($request->getHeaderLine('Content-Type'), 'application/json')) {
            $body = $request->getBody();
            $contents = $body->getContents();
            $parsedBody = $this->parseJson($contents);
            if ($parsedBody !== null) {
                $request = $request->withParsedBody($parsedBody);
            }
            $body->rewind();
        }
        return $handler->handle($request);
    }

    private function parseJson(string $json): ?array
    {
        if (empty($json)) {
            return null;
        }
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('JSON parse error: ' . json_last_error_msg());
            return null;
        }
        return is_array($data) ? $data : null;
    }
}
