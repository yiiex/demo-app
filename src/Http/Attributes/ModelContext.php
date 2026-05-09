<?php

namespace App\Http\Attributes;

use Attribute;
use Yiisoft\Hydrator\Attribute\Parameter\ParameterAttributeInterface;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class ModelContext implements ParameterAttributeInterface
{
    public function __construct(
        public string  $class,
        public ?string $routeKey = null,
        public array   $with = [],
        public ?string $scenario = null,
        public ?string $policy = null,
    ) {}

    public function getResolver(): string
    {
        return ModelContextResolver::class;
    }
}
