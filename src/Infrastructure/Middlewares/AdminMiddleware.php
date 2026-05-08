<?php

namespace App\Infrastructure\Middlewares;

use App\Http\Helpers\ResponseHelper;
use App\Models\User;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Psr\Http\Server\{MiddlewareInterface, RequestHandlerInterface};
use Yiisoft\User\CurrentUser;

class AdminMiddleware implements MiddlewareInterface
{
    public function __construct(protected CurrentUser $user, protected ResponseHelper $response)
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->user->getIdentity()?->can(User::ROLE_ADMIN)) {
            $this->response->error(403);
        }
        return $handler->handle($request);
    }
}
