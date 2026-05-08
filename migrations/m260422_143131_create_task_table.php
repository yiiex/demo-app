<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration {
    public function safeUp(): void
    {
        $this->createTable('task', [
            'id' => 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
            'name' => 'varchar(255) NOT NULL',
            'description' => 'text DEFAULT NULL',
            'user_id' => 'int(11) DEFAULT NULL',
            'project_id' => 'int(11) DEFAULT NULL',
            'deadline' => 'timestamp DEFAULT NULL',
            'estimated_time' => 'int(11) DEFAULT NULL',
            'status' => 'varchar(20) DEFAULT NULL',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
        ]);

        $this->addForeignKey('fk_task_user_id', 'task', 'user_id', 'user', 'id', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_task_project_id', 'task', 'project_id', 'project', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_task_user_id', 'task');
        $this->dropForeignKey('fk_task_project_id', 'task');
        $this->dropTable('task');
    }
};
