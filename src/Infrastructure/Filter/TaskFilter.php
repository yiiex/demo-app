<?php

namespace App\Infrastructure\Filter;

use App\Models\User;
use App\Shared\Filter\{Filter, FilterMethod};
use Yii1x\ActiveRecord\ConditionBuilder;
use Yii1x\ActiveRecord\QueryBuilder;
use Yiisoft\User\CurrentUser;

class TaskFilter extends Filter
{
    public ?string $search = null;
    public ?bool $myTasks = null;
    public array $with = ['users_r'];

    public function __construct(protected CurrentUser $user)
    {

    }

    #[FilterMethod(
        rules: [['search', 'safe', 'on' => 'filter']],
    )]
    public function filterSearch(QueryBuilder $queryBuilder): QueryBuilder
    {
        if ($this->search) {
            $queryBuilder
                ->like('t.name', '%' . $this->search . '%')
                ->like('t.description', '%' . $this->search . '%', 'OR');
        }
        if (!$this->user->getIdentity()->can(User::ROLE_ADMIN)) {
            $queryBuilder->where(function (ConditionBuilder $cb) {
                $cb->where('t.user_id', $this->user->getId())
                    ->whereRelation('user_links_r', fn(ConditionBuilder $cb) => $cb
                        ->where('user_links_r.user_id', $this->user->getId()), 'OR');
            });
        }
        return $queryBuilder;
    }

    #[FilterMethod(
        rules: [['myTasks', 'boolean', 'allowEmpty' => true, 'on' => 'filter']],
    )]
    public function myTasks(QueryBuilder $queryBuilder): QueryBuilder
    {
        if ($this->myTasks) {
            $queryBuilder
                ->whereRelation('user_links_r', fn(ConditionBuilder $cb) => $cb
                    ->where('user_links_r.user_id', $this->user->getId()));
        }
        return $queryBuilder;
    }

    public function attributeNames(): array
    {
        return ['search'];
    }

    public function attributeLabels(): array
    {
        return [
            'search' => 'Search',
        ];
    }
}
