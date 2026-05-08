<?php

namespace App\Models;

use Yii1x\ActiveRecord\Attributes\{Database, Table};

#[Database('db_main')]
#[Table('project_user')]
class ProjectUser extends BaseModel
{
    public function rules(): array
    {
        return [
            ['user_id', 'exist', 'className' => User::class, 'attributeName' => 'id', 'on' => 'insert'],
            ['user_id', 'unique', 'criteria' => [
                'condition' => 't.project_id = :project_id',
                'params' => [':project_id' => $this->project_id]
            ], 'on' => 'insert'],
        ];
    }

    public function relations(): array
    {
        return [
            'user_r' => [self::BELONGS_TO, User::class, 'user_id'],
            'project_r' => [self::BELONGS_TO, Project::class, 'project_id'],
        ];
    }

    public function jsonSerialize(): array
    {
        return parent::jsonSerialize() + [
                'user' => $this->whenLoaded('user_r'),
            ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User',
            'project_id' => 'Project ID',
            'user_r' => 'User',
        ];
    }
}
