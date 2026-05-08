<?php

namespace App\Shared\Caster\Casts;

use App\Shared\Caster\Contracts\CastInterface;
use JsonException;

class JsonCast implements CastInterface
{
    public function get(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            try {
                return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException) {
                return $value;
            }
        }

        return $value;
    }

    public function set(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            try {
                json_decode($value, true, 512, JSON_THROW_ON_ERROR);
                return $value;
            } catch (JsonException) {
            }
        }

        try {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $value;
        }
    }
}
