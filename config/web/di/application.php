<?php

declare(strict_types=1);

use App\Http\NotFound\NotFoundHandler;
use App\Infrastructure\Middlewares\DebugMiddleware;
use App\Infrastructure\Middlewares\DomainExceptionMiddleware;
use App\Infrastructure\Middlewares\InertiaMiddleware;
use App\Infrastructure\Middlewares\JsonBodyParserMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use Yiiex\Inertia\Inertia;
use Yiiex\Inertia\SimpleInertia;
use Yiisoft\Csrf\CsrfTokenMiddleware;
use Yiisoft\DataResponse\Middleware\FormatDataResponse;
use Yiisoft\Definitions\DynamicReference;
use Yiisoft\Definitions\Reference;
use Yiisoft\ErrorHandler\Middleware\ErrorCatcher;
use Yiisoft\Input\Http\HydratorAttributeParametersResolver;
use Yiisoft\Input\Http\RequestInputParametersResolver;
use Yiisoft\Middleware\Dispatcher\CompositeParametersResolver;
use Yiisoft\Middleware\Dispatcher\MiddlewareDispatcher;
use Yiisoft\Middleware\Dispatcher\ParametersResolverInterface;
use Yiisoft\RequestProvider\RequestCatcherMiddleware;
use Yiisoft\Router\Middleware\Router;
use Yiisoft\Session\SessionMiddleware;
use Yiisoft\User\CurrentUser;
use Yiisoft\User\Method\WebAuth;
use Yiisoft\Yii\Http\Application;

/** @var array $params */

return [
    Application::class => [
        '__construct()' => [
            'dispatcher' => DynamicReference::to([
                'class' => MiddlewareDispatcher::class,
                'withMiddlewares()' => [
                    [
                        ErrorCatcher::class,
                        SessionMiddleware::class,
                        CsrfTokenMiddleware::class,
                        FormatDataResponse::class,
                        RequestCatcherMiddleware::class,
                        JsonBodyParserMiddleware::class,
                        DebugMiddleware::class,
                        InertiaMiddleware::class,
                        DomainExceptionMiddleware::class,
                        Router::class,
                    ],
                ],
            ]),
            'fallbackHandler' => Reference::to(NotFoundHandler::class),
        ],
    ],
    WebAuth::class => [
        'class' => WebAuth::class,
        '__construct()' => [
            'currentUser' => Reference::to(CurrentUser::class),
            'responseFactory' => Reference::to(ResponseFactoryInterface::class),
        ],
        'withAuthUrl()' => ['/user/login'],
    ],
    ParametersResolverInterface::class => [
        'class' => CompositeParametersResolver::class,
        '__construct()' => [
            Reference::to(HydratorAttributeParametersResolver::class),
            Reference::to(RequestInputParametersResolver::class),
        ],
    ],
    Inertia::class => [
        'class' => SimpleInertia::class,
    ],
];
