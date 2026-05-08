<?php

namespace App\Shared\Caster;

use App\Shared\Caster\Contracts\{CasterInterface, CastInterface};
use Psr\Container\ContainerInterface;

class Caster implements CasterInterface
{
    /** @var array<string, CastInterface> */
    protected array $casts = [];

    public function __construct(protected readonly ContainerInterface $container)
    {
    }

    public function apply(mixed $value, string $cast, string $method): mixed
    {
        $castInstance = $this->resolveCast($cast);
        return $castInstance->{$method}($value);
    }

    public function applyMany(array $attributes, string $method, array $config = []): array
    {
        foreach ($config as $name => $cast) {
            if (isset($attributes[$name])) {
                $attributes[$name] = $this->apply($attributes[$name], $cast, $method);
            }
        }
        return $attributes;
    }

    protected function resolveCast(string $cast): CastInterface
    {
        if (isset($this->casts[$cast])) {
            return $this->casts[$cast];
        }

        if ($this->container->has($cast)) {
            /** @var CastInterface $instance */
            $instance = $this->container->get($cast);
        } else {
            if (!class_exists($cast)) {
                throw new \Exception("Class $cast does not exist");
            }
            $instance = new $cast();
        }

        if (!$instance instanceof CastInterface) {
            throw new \Exception('Cast must be an instance of CastInterface');
        }

        return $this->casts[$cast] = $instance;
    }
}
