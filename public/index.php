<?php

declare(strict_types=1);

use Psr\Log\LogLevel;
use Yiisoft\Config\Modifier\RemoveFromVendor;
use Yiisoft\ErrorHandler\ErrorHandler;
use Yiisoft\ErrorHandler\Renderer\HtmlRenderer;
use Yiisoft\Log\Logger;
use Yiisoft\Log\Target\File\FileTarget;
use Yiisoft\Yii\Runner\Http\HttpApplicationRunner;

$root = dirname(__DIR__);

require_once $root . '/vendor/autoload.php';

$env = new App\Environment();
$env->loadFromFile($root . '/.env');

/**
 * @psalm-var string $_SERVER ['REQUEST_URI']
 */
// PHP built-in server routing.
if (PHP_SAPI === 'cli-server') {
    // Serve static files as is.
    /** @var string $path */
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file(__DIR__ . $path)) {
        return false;
    }

    // Explicitly set for URLs with dot.
    $_SERVER['SCRIPT_NAME'] = '/index.php';
}

// Run HTTP application runner
$runner = new HttpApplicationRunner(
    rootPath: $root,
    debug: $env->get('APP_DEBUG'),
    checkEvents: $env->get('APP_DEBUG'),
    environment: $env->get('APP_ENV'),
    configModifiers: [
        RemoveFromVendor::keys(
            ['yiisoft/yii-debug', 'collectors'],
            ['yiisoft/yii-debug', 'collectors.web'],
        )->package('yiisoft/yii-debug'),
    ],
    temporaryErrorHandler: new ErrorHandler(
        new Logger(
            [
                (new FileTarget($root . '/runtime/logs/app-container-building.log'))->setLevels([
                    LogLevel::EMERGENCY,
                    LogLevel::ERROR,
                    LogLevel::WARNING,
                ]),
            ],
        ),
        new HtmlRenderer(),
    ),
);
$runner->run();
