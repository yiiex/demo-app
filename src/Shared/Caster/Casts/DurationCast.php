<?php

namespace App\Shared\Caster\Casts;

use App\Shared\Caster\Contracts\CastInterface;

class DurationCast implements CastInterface
{

    public function get(mixed $value): ?string
    {
        if(!$value || !is_numeric($value)) {
            return null;
        }
        $hours = floor($value / 3600);
        $minutes = floor(($value % 3600) / 60);

        return implode(' ', array_filter([
            $hours > 0 ? $hours . 'h' : null,
            $minutes > 0 ? $minutes . 'm' : null,
        ]));
    }

    public function set(mixed $value): int|float
    {
        if (empty($value)) {
            return 0;
        }
        $seconds = 0;
        if (preg_match('/(\d+)h/', $value, $h)) {
            $seconds += (int)$h[1] * 3600;
        }
        if (preg_match('/(\d+)m/', $value, $m)) {
            $seconds += (int)$m[1] * 60;
        }
        return $seconds;
    }
}
