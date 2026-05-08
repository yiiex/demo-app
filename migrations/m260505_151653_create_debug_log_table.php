<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration {
    public function safeUp(): void
    {
        $this->createTable('debug_log', [
            'id' => 'varchar(36) NOT NULL PRIMARY KEY',
            'user_id' => 'int(11) DEFAULT NULL',
            'data' => 'json NOT NULL',
        ]);

        $this->addForeignKey('fk_debug_log_user', 'debug_log', 'user_id', 'user', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_debug_log_user', 'debug_log');
        $this->dropTable('debug_log');
    }
};
