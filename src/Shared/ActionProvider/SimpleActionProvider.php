<?php

namespace App\Shared\ActionProvider;

use Closure;

abstract class SimpleActionProvider implements ActionProvider
{
    protected ?array $only = null;
    protected ?Closure $filter = null;

    public function preparedActions($model): array
    {
        $actions = [];
        foreach ($this->filter ? array_filter($this->actions($model), $this->filter) : $this->actions($model) as $action) {
            $newAction = $this->prepareAction($model, $action);
            if ($newAction->can()) {
                $actions[] = $newAction->toArray();
            }
        }
        return $actions;
    }

    public function filter(Closure $filter): static
    {
        $this->filter = $filter;
        return $this;
    }

    public function can($model, string $action): bool
    {
        $action = array_find($this->actions($model), fn($a) => $a->name === $action);
        return $action && $this->prepareAction($model, $action)->can();
    }

    protected function prepareAction($model, Action $action): Action
    {
        $action = clone $action;
        $action->model($model);
        if (property_exists($this, 'user')) {
            $action->user($this->user);
        }
        return $action;
    }
}
