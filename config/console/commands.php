<?php

declare(strict_types=1);

use App\Console\HelloCommand;
use App\Console\UserCommand;
use Yii1x\ActiveRecord\Console\MigrateCommand;

return [
    'hello' => HelloCommand::class,
    'migrate' => MigrateCommand::class,
    'user' => UserCommand::class,
];
