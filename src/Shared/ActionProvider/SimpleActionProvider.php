<?php

namespace App\Shared\ActionProvider;

abstract class SimpleActionProvider implements ActionProvider
{
    protected ?array $only = null;

    public function preparedActions($model): array
    {
        $actions = [];
        foreach ($this->actions($model) as $action) {
            if ($this->only && !in_array($action->name, $this->only)) {
                continue;
            }
            $newAction = $this->prepareAction($model, $action);
            if ($newAction->can()) {
                $actions[] = $newAction->toArray();
            }
        }
        return $actions;
    }

    public function only(array $actions): static
    {
        $this->only = $actions;
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
