<?php

namespace App\Shared\ActionProvider;

interface ActionProvider
{
    public function actions($model): array;

    public function preparedActions($model): array;
}
