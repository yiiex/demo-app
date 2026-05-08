<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration
{
    public function safeUp(): void
    {
        $this->createTable('task_time_entry', [
            'id' => 'pk',
            'task_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) DEFAULT NULL',
            'duration' => 'int(11) NOT NULL',
            'description' => 'text DEFAULT NULL',
            'date' => 'date NOT NULL',
            'created_at' => 'timestamp NOT NULL',
            'updated_at' => 'timestamp NOT NULL',
        ]);

        $this->addForeignKey('fk_task_time_entry_task', 'task_time_entry', 'task_id', 'task', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_task_time_entry_user', 'task_time_entry', 'user_id', 'user', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_task_time_entry_task', 'task_time_entry');
        $this->dropForeignKey('fk_task_time_entry_user', 'task_time_entry');
        $this->dropTable('task_time_entry');
    }
};
