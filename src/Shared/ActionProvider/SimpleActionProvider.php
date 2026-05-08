<?php

namespace App\Shared\ActionProvider;

abstract class SimpleActionProvider implements ActionProvider
{
    public function preparedActions($model, array $exclude = []): array
    {
        $actions = [];
        foreach ($this->actions($model) as $action) {
            if (in_array($action->name, $exclude)) {
                continue;
            }
            $newAction = $this->prepareAction($model, $action);
            if ($newAction->can()) {
                $actions[] = $newAction->toArray();
            }
        }
        return $actions;
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
