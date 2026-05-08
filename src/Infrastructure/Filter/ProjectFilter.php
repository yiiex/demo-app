<?php

namespace App\Infrastructure\Filter;

use App\Shared\Filter\{Filter, FilterMethod};
use Yii1x\ActiveRecord\QueryBuilder;

class ProjectFilter extends Filter
{
    public ?string $search = null;
    protected array $with = ['users_count'];

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
