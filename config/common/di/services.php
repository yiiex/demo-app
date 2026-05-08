<?php

use App\Debug\QueryCollector;
use App\Http\Helpers\HttpContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ListProviders\ProjectListProvider;
use App\Infrastructure\ListProviders\UserListProvider;
use App\Infrastructure\View\NavManager;
use App\Infrastructure\View\SimpleNavManager;
use App\Infrastructure\View\ViteViewInjection;
use App\Shared\Caster\Caster;
use App\Shared\Caster\Contracts\CasterInterface;
use App\Shared\ListProvider\ListProviderManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Yii1x\ActiveRecord\Contracts\MigrationManagerInterface;
use Yii1x\ActiveRecord\Migration\MigrationManager;
use Yiisoft\Definitions\Reference;
use Yiisoft\Injector\Injector;

return [
    MigrationManagerInterface::class => [
        'class' => MigrationManager::class,
        '__construct()' => [
            'tableName' => 'tbl_migration',
            'connectionName' => 'db_main',
            'migrationPath' => dirname(__DIR__, 3) . '/migrations',
        ],
    ],
    QueryCollector::class => [
        'class' => QueryCollector::class,
        '__construct()' => [
            'logger' => Reference::to(LoggerInterface::class),
        ],
    ],
    ViteViewInjection::class => [
        'class' => ViteViewInjection::class,
        '__construct()' => [
            'viteAssetPath' => dirname(__DIR__, 3) . '/public',
            'isDevMode' => true,
        ],
    ],
    HttpContext::class => [
        'class' => HttpContext::class,
    ],
    ResponseHelper::class => [
        'class' => ResponseHelper::class,
    ],
    NavManager::class => [
        'class' => SimpleNavManager::class,
        'withHomeBreadcrumb()' => [],
    ],
    ListProviderManager::class => [
        'class' => ListProviderManager::class,
        '__construct()' => [
            'injector' => Reference::to(Injector::class),
        ],
        'withProviders()' => [
            'providers' => [
                'user.autocomplete' => UserListProvider::class,
                'project.autocomplete' => ProjectListProvider::class,
            ],
        ],
    ],
    CasterInterface::class => [
        'class' => Caster::class,
        '__construct()' => [
            'injector' => Reference::to(ContainerInterface::class),
        ],
    ],
];
