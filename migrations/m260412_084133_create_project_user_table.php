<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration
{
    public function safeUp(): void
    {
        $this->createTable('project_user', [
            'id' => 'pk',
            'project_id' => 'int(11) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'created_at' => 'timestamp NOT NULL',
            'updated_at' => 'timestamp NOT NULL',
        ]);
        $this->createIndex('idx_project_user_unique', 'project_user', ['project_id', 'user_id'], true);
        $this->addForeignKey(
            'fk_project_user_project',
            'project_user',
            'project_id',
            'project',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_project_user_user',
            'project_user',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function safeDown(): void
    {
        $this->dropForeignKey('fk_project_user_project', 'project_user');
        $this->dropForeignKey('fk_project_user_user', 'project_user');
        $this->dropTable('project_user');
    }
};
