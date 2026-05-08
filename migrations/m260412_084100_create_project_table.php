<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration
{
    public function safeUp(): void
    {
        $this->createTable('project', [
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'description' => 'text DEFAULT NULL',
            'user_id' => 'int(11) DEFAULT NULL',
            'created_at' => 'timestamp NOT NULL',
            'updated_at' => 'timestamp NOT NULL',
        ]);

        $this->addForeignKey(
            'fk_project_user',
            'project',
            'user_id',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_project_user', 'project');
        $this->dropTable('project');
    }
};
