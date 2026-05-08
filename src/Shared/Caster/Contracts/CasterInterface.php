<?php

namespace App\Shared\Caster\Contracts;

interface CasterInterface
{
    public function apply(mixed $value, string $cast, string $method): mixed;

    public function applyMany(array $attributes, string $method, array $config = []): array;
}
