<?php

namespace App\Infrastructure\Filter;

use App\Shared\Filter\Filter;
use App\Shared\Filter\FilterMethod;
use Yii1x\ActiveRecord\{ConditionBuilder, QueryBuilder};
use Yiisoft\User\CurrentUser;

class TimeSheetFilter extends Filter
{
    public ?string $date = null;

    public function __construct(protected CurrentUser $user)
    {

    }

    #[FilterMethod(
        rules: [['date', 'date', 'allowEmpty' => true, 'format' => 'Y-m-d', 'on' => 'filter']],
    )]
    public function filterDate(QueryBuilder $queryBuilder): QueryBuilder
    {
        if ($this->date) {
            $queryBuilder->whereRelation('time_entries', function (ConditionBuilder $query) {
                $query->where('time_entries.date', $this->date)
                    ->where('time_entries.user_id', $this->user->getId());
            });
        }
        return $queryBuilder;
    }

    public function attributeNames(): array
    {
        return ['date'];
    }
}
