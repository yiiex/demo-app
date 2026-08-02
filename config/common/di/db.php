<?php
/** @var array $params */
return [
    'db_main' => [
        'class' => \Yii1x\ActiveRecord\Db\DbConnection::class,
        '__construct()' => [
            'dsn' => $params['db']['dsn'],
            'username' => $params['db']['username'],
            'password' => $params['db']['password'],
            'connectionName' => 'db_main',
        ],
        '$schemaCachingDuration' => 3600,
    ],
];
