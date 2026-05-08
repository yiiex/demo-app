<?php

namespace App\Infrastructure\Middlewares;

use App\Environment;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yii1x\Inertia\Inertia;
use Yiisoft\Yii\Debug\Debugger;

class DebugMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected Debugger    $debugger,
        protected Inertia     $inertia,
        protected Environment $env,
    )
    {
    }


    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->env->isDebug()) {
            $this->inertia->share(['debugId' => $this->debugger->getId()]);
        }
        $response = $handler->handle($request);
        if ($this->env->isDebug() && $response->hasHeader('X-DEBUG-IGNORE') != 'true') {
            $response = $response->withHeader('X-DEBUG-ID', $this->debugger->getId());
        }
        return $response;
    }
}
