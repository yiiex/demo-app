<?php

declare(strict_types=1);

use App\Debug\DatabaseDebugStorage;
use App\Repositories\IdentityRepository;
use App\Shared\ApplicationParams;
use Psr\SimpleCache\CacheInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;
use Yiisoft\Cache\Apcu\ApcuCache;
use Yiisoft\Definitions\Reference;
use Yiisoft\Session\Session;
use Yiisoft\Session\SessionInterface;
use Yiisoft\User\CurrentUser;
use Yiisoft\Yii\Debug\Storage\StorageInterface;

/** @var array $params */

return [
    ApplicationParams::class => [
        '__construct()' => [
            'name' => $params['application']['name'],
            'charset' => $params['application']['charset'],
            'locale' => $params['application']['locale'],
        ],
    ],
    SessionInterface::class => [
        'class' => Session::class,
        '__construct()' => [
            $params['session']['options'] ?? [],
            $params['session']['handler'] ?? null,
        ],
    ],
    IdentityRepositoryInterface::class => IdentityRepository::class,
    CurrentUser::class => [
        'withSession()' => [Reference::to(SessionInterface::class)]
    ],
    StorageInterface::class => DatabaseDebugStorage::class,
    CacheInterface::class => ApcuCache::class,
];
