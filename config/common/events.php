<?php

use App\Debug\QueryCollector;
use App\Environment;
use Psr\Container\ContainerInterface;
use Yii1x\ActiveRecord\Events\EndQueryEvent;
use Yii1x\ActiveRecord\ORMContext;
use Yiisoft\Yii\Console\Event\ApplicationStartup as ConsoleApplicationStartup;
use Yiisoft\Yii\Http\Event\ApplicationStartup;

return [
    ApplicationStartup::class => $handlers = [
        function (ContainerInterface $container, Environment $env) {
            ORMContext::bootstrap($container, true, $env->isDebug());
        },
    ],
    ConsoleApplicationStartup::class => $handlers,
    EndQueryEvent::class => [
        [QueryCollector::class, 'listenQuery'],
    ],
];
