<?php

namespace App\Infrastructure\ListProviders;

use App\Models\Project;
use App\Shared\ListProvider\ListProvider;
use Yii1x\ActiveRecord\ConditionBuilder;
use Yiisoft\User\CurrentUser;

class ProjectListProvider implements ListProvider
{
    public function __construct(protected CurrentUser $currentUser)
    {

    }

    public function fetch(array $context = []): array
    {
        $query = Project::queryBuilder()->limit(50);
        $query->whereRelation('user_links', fn(ConditionBuilder $cb) => $cb->where('user_links.user_id', $this->currentUser->getId()));
        if (isset($context['search']) && is_string($context['search'])) {
            $query->like('name', $context['search']);
        } else if (isset($context['value'])) {
            $query->whereNotIn('t.id', (array)$context['value']);
        }
        if (isset($context['value'])) {
            $query->whereIn('t.id', (array)$context['value'], operator: 'OR');
        }
        return array_map(fn(Project $model) => [
            'key' => $model->id,
            'label' => $model->name,
        ], $query->findAll());
    }
}
