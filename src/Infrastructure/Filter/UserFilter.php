<?php

namespace App\Infrastructure\Filter;

use App\Shared\Filter\Filter;
use App\Shared\Filter\FilterMethod;
use Yii1x\ActiveRecord\QueryBuilder;

class UserFilter extends Filter
{
    public ?string $fullName = null;

    #[FilterMethod(
        rules: [['fullName', 'safe', 'on' => 'filter']],
    )]
    public function filterFullName(QueryBuilder $queryBuilder): QueryBuilder
    {
        if ($this->fullName) {
            $queryBuilder->scopes(['fullName' => [$this->fullName]]);
        }
        return $queryBuilder;
    }

    public function attributeNames(): array
    {
        return ['fullName'];
    }

    public function attributeLabels(): array
    {
        return [
            'fullName' => 'Full Name',
        ];
    }
}
