<?php

declare(strict_types=1);

use App\Debug\QueryCollector;
use App\Infrastructure\View\ViteViewInjection;
use App\Shared\ApplicationParams;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Definitions\Reference;
use Yiisoft\Middleware\Dispatcher\Debug\MiddlewareCollector;
use Yiisoft\Router\CurrentRoute;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Yii\Debug\Collector\EventCollector;
use Yiisoft\Yii\Debug\Collector\HttpClientCollector;
use Yiisoft\Yii\Debug\Collector\Web\RequestCollector;
use Yiisoft\Yii\Debug\Collector\Web\WebAppInfoCollector;
use Yiisoft\Yii\View\Renderer\CsrfViewInjection;

return [
    'application' => require __DIR__ . '/application.php',
    'session' => [
        'options' => [
            'cookie_secure' => false,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'cookie_lifetime' => 0,
            'cookie_path' => '/',
            'cookie_domain' => '',
        ],
        'handler' => null,
    ],
    'yiisoft/aliases' => [
        'aliases' => require __DIR__ . '/aliases.php',
    ],
    'yiisoft/view' => [
        'basePath' => null,
        'parameters' => [
            'assetManager' => Reference::to(AssetManager::class),
            'applicationParams' => Reference::to(ApplicationParams::class),
            'aliases' => Reference::to(Aliases::class),
            'urlGenerator' => Reference::to(UrlGeneratorInterface::class),
            'currentRoute' => Reference::to(CurrentRoute::class),
        ],
    ],
    'yiisoft/yii-view-renderer' => [
        'viewPath' => '@views',
        'layout' => '@views/layouts/main.php',
        'injections' => [
            Reference::to(CsrfViewInjection::class),
            Reference::to(ViteViewInjection::class),
        ],
    ],
    'yiisoft/yii-debug' => [
        'collectors' => [],
        'collectors.web' => [
            RequestCollector::class,
            QueryCollector::class,
            WebAppInfoCollector::class,
        ],
        'collectors.console' => [],
    ],
];
