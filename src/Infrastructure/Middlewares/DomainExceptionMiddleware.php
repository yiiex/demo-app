<?php

namespace App\Infrastructure\Middlewares;

use App\Http\Exceptions\{ForbiddenException, NotFoundException, ValidationException};
use App\Http\Helpers\ResponseHelper;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};

final readonly class DomainExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ResponseHelper $responseHelper,
    ) {}

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        try {
            return $handler->handle($request);
        } catch (NotFoundException $e) {
            return $this->responseHelper->error(404, $e->getMessage());
        } catch (ForbiddenException $e) {
            return $this->responseHelper->error(403, $e->getMessage());
        } catch (ValidationException $e) {
            return $this->responseHelper->json([
                'success' => false,
                'errors' => $e->getErrors(),
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
