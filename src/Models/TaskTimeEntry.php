<?php

namespace App\Models;

use App\Shared\Behaviors\CastBehavior;
use App\Shared\Caster\Casts\DurationCast;
use App\Shared\Rules\DurationRule;
use Yii1x\ActiveRecord\Attributes\{Database, Table};

#[Database('db_main')]
#[Table('task_time_entry')]
class TaskTimeEntry extends BaseModel
{
    public function behaviors(): array
    {
        return [
            CastBehavior::class => [
                'class' => CastBehavior::class,
                'attributes' => [
                    'duration' => DurationCast::class,
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['task_id, user_id, duration, date', 'required', 'on' => 'insert, update'],
            ['task_id', 'exist', 'className' => Task::class, 'attributeName' => 'id', 'on' => 'insert, update'],
            ['user_id', 'exist', 'className' => User::class, 'attributeName' => 'id', 'on' => 'insert, update'],
            ['duration', DurationRule::class, 'allowEmpty' => true, 'on' => 'insert, update'],
            ['date', 'date', 'format' => 'Y-m-d', 'on' => 'insert, update'],
            ['description', 'length', 'max' => 4000, 'on' => 'insert, update'],
        ];
    }

    public function relations(): array
    {
        return [
            'task_r' => [self::BELONGS_TO, Task::class, 'task_id'],
            'user_r' => [self::BELONGS_TO, User::class, 'user_id'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task',
            'user_id' => 'User',
            'duration' => 'Duration',
            'description' => 'Description',
            'date' => 'Date',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
                'user' => $this->whenLoaded('user_r'),
            ];
    }
}
