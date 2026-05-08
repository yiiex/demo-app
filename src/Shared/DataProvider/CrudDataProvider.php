<?php

namespace App\Shared\DataProvider;

use App\Shared\ActionProvider\Traits\WithActions;
use App\Shared\Filter\Traits\WithFilter;

class CrudDataProvider extends ActiveDataProvider
{
    use WithActions, WithFilter;

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->items(),
            'actions' => $this->actions(),
            'filter' => $this->filter,
            'meta' => [
                'total' => $this->totalCount(),
                'page' => $this->currentPage,
                'perPage' => $this->perPage,
                'count' => $this->count(),
            ],
        ];
    }
}
