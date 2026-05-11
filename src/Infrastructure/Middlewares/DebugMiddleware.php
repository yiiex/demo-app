<?php

namespace App\Infrastructure\Middlewares;

use App\Environment;
use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiiex\Inertia\Inertia;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\Debug\Debugger;

class DebugMiddleware implements MiddlewareInterface
{
    public bool $isDebug {
        get => $this->env->isDebug() && !$this->user->isGuest() && $this->user->getIdentity()->can(User::ROLE_ADMIN);
    }

    public function __construct(
        protected Debugger    $debugger,
        protected Inertia     $inertia,
        protected Environment $env,
        protected CurrentUser $user,
    )
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isDebug) {
            $this->inertia->share(['debugId' => $this->debugger->getId()]);
        }
        $response = $handler->handle($request);
        if ($this->isDebug && $response->hasHeader('X-DEBUG-IGNORE') != 'true') {
            $response = $response->withHeader('X-DEBUG-ID', $this->debugger->getId());
        }
        return $response;
    }
}
