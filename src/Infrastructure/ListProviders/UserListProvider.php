<?php

namespace App\Infrastructure\ListProviders;

use App\Models\User;
use App\Shared\ListProvider\ListProvider;
use Yii1x\ActiveRecord\ConditionBuilder;

class UserListProvider implements ListProvider
{

    public function fetch(array $context = []): array
    {
        $query = User::queryBuilder()->limit(50);
        if (isset($context['search']) && is_string($context['search'])) {
            $query->applyScopes(['fullName' => [$context['search']]]);
        } else if (isset($context['value'])) {
            $query->whereNotIn('t.id', (array)$context['value']);
        }
        if (isset($context['value'])) {
            $query->whereIn('t.id', (array)$context['value'], operator: 'OR');
        }
        return array_map(fn(User $model) => [
            'key' => $model->id,
            'label' => $model->fullName,
        ], $query->findAll());
    }
}
