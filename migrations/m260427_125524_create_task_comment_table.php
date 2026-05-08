<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration
{
    public function safeUp(): void
    {
        $this->createTable('task_comment', [
            'id' => 'pk',
            'task_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) DEFAULT NULL',
            'comment' => 'text NOT NULL',
            'created_at' => 'timestamp NOT NULL',
            'updated_at' => 'timestamp NOT NULL',
        ]);

        $this->addForeignKey('fk_task_comment_task', 'task_comment', 'task_id', 'task', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_task_comment_user', 'task_comment', 'user_id', 'user', 'id', 'SET NULL', 'CASCADE');
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_task_comment_task', 'task_comment');
        $this->dropForeignKey('fk_task_comment_user', 'task_comment');
        $this->dropTable('task_comment');
    }
};
