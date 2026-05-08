<?php

namespace App\Shared\Filter\Traits;

use App\Shared\Filter\Filter;

trait WithFilter
{
    protected ?Filter $filter = null;

    public function withFilter(Filter $filter): static
    {
        $this->filter = $filter;
        $this->filter->apply($this->queryBuilder);
        return $this;
    }
}
