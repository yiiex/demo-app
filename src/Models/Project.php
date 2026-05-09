<?php

namespace App\Models;

use Yii1x\ActiveRecord\Attributes\{Database, Table};
use Yii1x\ActiveRecord\Model\Event;

#[Database('db_main')]
#[Table('project')]
class Project extends BaseModel
{
    public function rules(): array
    {
        return [
            ['name, description', 'required', 'on' => 'insert, update'],
            ['name', 'unique', 'on' => 'insert, update'],
        ];
    }

    public function relations(): array
    {
        return [
            'users_count' => [self::STAT, ProjectUser::class, 'project_id'],
            'user_links_r' => [self::HAS_MANY, ProjectUser::class, 'project_id'],
            'owner_r' => [self::BELONGS_TO, User::class, 'user_id'],
        ];
    }

    public function beforeSave(): bool
    {
        if ($this->isNewRecord) {
            $this->onAfterSave = function (Event $e) {
                $owner = new ProjectUser('insert');
                $owner->setAttributes([
                    'user_id' => $this->user_id,
                    'project_id' => $e->sender->id,
                ], false);
                $owner->save(false);
            };
        }
        return parent::beforeSave();
    }

    public function isOwner(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isMember(User $user): bool
    {
        return $this->user_links_r && array_any($this->user_links_r, fn($link) => $link->user_id == $user->id);
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
                'users_count' => $this->whenLoaded('users_count', 0),
            ];
    }
}
