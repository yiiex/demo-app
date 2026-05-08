<?php

namespace App\Shared\ListProvider;

use Yiisoft\Injector\Injector;

class ListProviderManager
{
    protected array $providers = [];

    public function __construct(protected Injector $injector)
    {

    }

    public function withProviders(array $providers): static
    {
        $this->providers = $providers;
        return $this;
    }

    public function get(string $id): ?ListProvider
    {
        if ($this->has($id)) {
            $provider = $this->injector->make($this->providers[$id]);
            if (!$provider instanceof ListProvider) {
                throw new \Exception("List provider '{$id}' must implement ListProviderInterface");
            }
            return $provider;
        }
        return null;
    }

    public function has(string $id): bool
    {
        return isset($this->providers[$id]);
    }
}
