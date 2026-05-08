<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration
{
	public function safeUp(): void
    {
        $this->createTable('task_user', [
            'id' => 'pk',
            'task_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'created_at' => 'timestamp NOT NULL',
            'updated_at' => 'timestamp NOT NULL',
        ]);
        $this->createIndex('idx_task_user_unique', 'task_user', ['task_id', 'user_id'], true);
        $this->addForeignKey(
            'fk_task_user_task',
            'task_user',
            'task_id',
            'task',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_task_user_user',
            'task_user',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function safeDown(): void
    {
        $this->dropForeignKey('fk_task_user_task', 'task_user');
        $this->dropForeignKey('fk_task_user_user', 'task_user');
        $this->dropTable('task_user');
	}
};
