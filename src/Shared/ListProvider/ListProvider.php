<?php

namespace App\Shared\ListProvider;

interface ListProvider
{
    public function fetch(array $context = []): array;
}
