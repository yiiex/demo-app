<?php

namespace App\Models;

use Yii1x\ActiveRecord\Attributes\{Database, Table};

#[Database('db_main')]
#[Table('task_comment')]
class TaskComment extends BaseModel
{
    public function rules(): array
    {
        return [
            ['task_id, comment', 'required', 'on' => 'insert, update'],
            ['task_id', 'exist', 'className' => Task::class, 'attributeName' => 'id', 'on' => 'insert, update'],
            ['user_id', 'exist', 'className' => User::class, 'attributeName' => 'id', 'allowEmpty' => true, 'on' => 'insert, update'],
            ['comment', 'length', 'max' => 4000, 'on' => 'insert, update'],
        ];
    }

    public function relations(): array
    {
        return [
            'task' => [self::BELONGS_TO, Task::class, 'task_id'],
            'user' => [self::BELONGS_TO, User::class, 'user_id'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task',
            'user_id' => 'User',
            'comment' => 'Comment',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
                'user' => $this->whenLoaded('user'),
            ];
    }
}
