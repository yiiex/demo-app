<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration {
    public function safeUp(): void
    {
        $this->createTable('user', [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'email' => 'varchar(255) NOT NULL',
            'password' => 'varchar(255) NOT NULL',
            'first_name' => 'varchar(255) DEFAULT NULL',
            'last_name' => 'varchar(255) DEFAULT NULL',
            'role' => 'varchar(25) DEFAULT \'user\'',
            'last_login_at' => 'timestamp DEFAULT NULL',
            'created_at' => 'timestamp NOT NULL',
            'updated_at' => 'timestamp NOT NULL',
        ]);
        $this->createIndex('idx_user_email', 'user', 'email', true);
    }

    public function safeDown(): void
    {
        $this->dropTable('user');
    }
};
