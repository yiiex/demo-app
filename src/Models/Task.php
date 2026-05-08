<?php

namespace App\Models;

use App\Shared\Behaviors\CastBehavior;
use App\Shared\Caster\Casts\DurationCast;
use App\Shared\Rules\DurationRule;
use Yii1x\ActiveRecord\Attributes\{Database, Table};

#[Database('db_main')]
#[Table('task')]
class Task extends BaseModel
{
    const string STATUS_PENDING = 'pending';
    const string STATUS_IN_PROGRESS = 'in_progress';
    const string STATUS_DONE = 'done';
    public ?array $users = null;
    public ?string $shortName {
        get => $this->name ? mb_strimwidth($this->name, 0, 50, '...') : null;
    }

    public ?float $progress {
        get {
            if (!$this->estimated_time || !$this->hasRelated('spent_time')) {
                return null;
            }
            $estimated = $this->asa(CastBehavior::class)->encode('estimated_time');
            $spent = $this->asa(CastBehavior::class)->encode('spent_time');
            return $estimated > 0 ? min(round(($spent / $estimated) * 100), 100) : null;
        }
    }

    public ?string $reference {
        get => $this->id ? 'TASK-' . $this->id : null;
    }

    public function behaviors(): array
    {
        return [
            CastBehavior::class => [
                'class' => CastBehavior::class,
                'attributes' => [
                    'estimated_time' => DurationCast::class,
                    'spent_time' => DurationCast::class,
                ],
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['name, project_id, users', 'required', 'on' => 'insert, update'],
            ['project_id', 'exist', 'className' => Project::class, 'attributeName' => 'id', 'on' => 'insert, update'],
            ['name', 'length', 'max' => 255, 'on' => 'insert, update'],
            ['description', 'length', 'max' => 4000, 'on' => 'insert, update'],
            ['deadline', 'date', 'format' => 'Y-m-d H:i:s', 'allowEmpty' => true, 'on' => 'insert, update'],
            ['estimated_time', DurationRule::class, 'allowEmpty' => true, 'on' => 'insert, update'],
        ];
    }

    public function relations(): array
    {
        return [
            'user_r' => [self::BELONGS_TO, User::class, 'user_id'],
            'project_r' => [self::BELONGS_TO, Project::class, 'project_id'],
            'user_links_r' => [self::HAS_MANY, TaskUser::class, 'task_id'],
            'users_r' => [self::MANY_MANY, User::class, 'task_user(task_id, user_id)'],
            'spent_time' => [self::STAT, TaskTimeEntry::class, 'task_id', 'select' => 'SUM(duration)'],
        ];
    }

    public function beforeSave(): bool
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_PENDING;
        }

        if ($this->scenario === 'take') {
            $this->status = self::STATUS_IN_PROGRESS;
        }

        if ($this->scenario === 'complete') {
            $this->status = self::STATUS_DONE;
        }

        if ($this->scenario === 'return') {
            $this->status = self::STATUS_PENDING;
        }

        return parent::beforeSave();
    }

    public function isAssignee(User $user): bool
    {
        return in_array($user->id, $this->hasRelated('users_r')
            ? array_column($this->users_r, 'id')
            : array_column($this->user_links_r, 'user_id'));
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Task name',
            'description' => 'Description',
            'user_id' => 'Creator',
            'status' => 'Status',
            'deadline' => 'Deadline',
            'estimated_time' => 'Estimated time',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
            'project_id' => 'Project',
            'project_r' => 'Project',
            'users' => 'Assignees',
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
                'shortName' => $this->shortName,
                'progress' => $this->progress,
                'spent_time' => $this->whenLoaded('spent_time'),
                'project_r' => $this->whenLoaded('project_r'),
                'users_r' => $this->whenLoaded('users_r', []),
                'user_links_r' => $this->whenLoaded('user_links_r', []),
            ];
    }
}
