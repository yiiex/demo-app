<?php

namespace App\Shared\ActionProvider;

use Closure;

interface ActionProvider
{
    public function actions($model): array;

    public function preparedActions($model): array;

    public function filter(Closure $filter): static;
}
