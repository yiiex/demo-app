<?php

namespace App\Models;

use Yii1x\ActiveRecord\Attributes\{Database, Table};

#[Database('db_main')]
#[Table('task_user')]
class TaskUser extends BaseModel
{
    public function rules(): array
    {
        return [
            ['user_id', 'exist', 'className' => User::class, 'attributeName' => 'id', 'on' => 'insert'],
            ['user_id', 'unique', 'criteria' => [
                'condition' => 't.task_id = :task_id',
                'params' => [':task_id' => $this->task_id]
            ], 'on' => 'insert'],
        ];
    }

    public function relations(): array
    {
        return [
            'user' => [self::BELONGS_TO, User::class, 'user_id'],
            'task' => [self::BELONGS_TO, Task::class, 'task_id'],
            'time_summary' => [self::HAS_ONE, TaskTimeSummary::class, ['user_id' => 'user_id', 'task_id' => 'task_id']],
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
                'user' => $this->whenLoaded('user'),
                'time_summary' => $this->whenLoaded('time_summary'),
            ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'task_id' => 'Task ID',
            'user' => 'User',
        ];
    }
}
