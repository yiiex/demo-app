<?php

namespace App\Shared\Caster\Casts;

use App\Shared\Caster\Contracts\CastInterface;
use DateTime;

class DateTimeCast implements CastInterface
{

    public function get(mixed $value): mixed
    {
        if (empty($value)) {
            return null;
        }

        try {
            return (new DateTime($value))->format('c');
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function set(mixed $value): mixed
    {
        if (empty($value)) {
            return null;
        }

        $date = date_create($value);
        if (!$date) {
            return $value; // не дата — оставляем, валидатор покажет ошибку
        }

        return $date->format('Y-m-d H:i:s');
    }
}
