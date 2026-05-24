<?php

namespace App\Infrastructure\Filter;

use App\Shared\Filter\Filter;
use App\Shared\Filter\FilterMethod;
use Yii1x\ActiveRecord\ConditionBuilder;
use Yii1x\ActiveRecord\QueryBuilder;

class ProjectUserFilter extends Filter
{
    public ?string $fullName = null;
    protected array $with = ['user', 'project'];

    #[FilterMethod(
        rules: [['fullName', 'safe', 'on' => 'filter']],
    )]
    public function search(QueryBuilder $queryBuilder): QueryBuilder
    {
        if ($this->fullName) {
            $queryBuilder->whereRelation('user', fn(ConditionBuilder $q) => $q
                ->scopes(['fullName' => [$this->fullName]]));
        }
        return $queryBuilder;
    }

    public function attributeNames(): array
    {
        return ['fullName'];
    }
}
