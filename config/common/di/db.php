<?php
return [
    'db_main' => [
        'class' => \Yii1x\ActiveRecord\Db\DbConnection::class,
        '__construct()' => [
            'dsn' => 'mysql:host=db;port=3306;dbname=aurora',
            'username' => 'root',
            'password' => 'root',
            'connectionName' => 'db_main',
        ],
        '$schemaCachingDuration' => 3600,
    ],
];
