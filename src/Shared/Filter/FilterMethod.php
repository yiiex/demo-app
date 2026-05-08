<?php

namespace App\Shared\Filter;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class FilterMethod
{
    public string $name;

    public function __construct(
        public array $rules,
    )
    {

    }
}
