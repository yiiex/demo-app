<?php

namespace App\Models;

use App\Shared\Behaviors\CastBehavior;
use App\Shared\Caster\Casts\DurationCast;
use Yii1x\ActiveRecord\Attributes\{Database, Table};

#[Database('db_main')]
#[Table('task_time_entry')]
class TaskTimeSummary extends BaseModel
{
    public function behaviors(): array
    {
        return [
            CastBehavior::class => [
                'class' => CastBehavior::class,
                'attributes' => [
                    'total_duration' => DurationCast::class,
                ],
            ],
        ];
    }

    public function primaryKey(): array
    {
        return ['user_id', 'task_id'];
    }

    public function tableName(): string
    {
        return 'task_time_summary';
    }
}
