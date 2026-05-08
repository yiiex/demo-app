<?php

namespace App\Shared\Caster\Contracts;

interface CastInterface
{
    public function get(mixed $value): mixed;

    public function set(mixed $value): mixed;
}
