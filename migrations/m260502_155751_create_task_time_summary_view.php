<?php

use Yii1x\ActiveRecord\Attributes\Database;
use Yii1x\ActiveRecord\Db\DbMigration;

return new
#[Database('db_main')]
class extends DbMigration
{
	public function safeUp(): void
    {
        $this->execute(<<<SQL
                CREATE OR REPLACE VIEW task_time_summary AS
                SELECT
                    user_id,
                    task_id,
                    CAST(SUM(duration) AS UNSIGNED) as total_duration
                FROM task_time_entry
                GROUP BY user_id, task_id
            SQL);
	}

	public function safeDown(): void
    {
        $this->execute("DROP VIEW IF EXISTS task_time_summary");
	}
};
