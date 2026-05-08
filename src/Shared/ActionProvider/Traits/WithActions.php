<?php

namespace App\Shared\ActionProvider\Traits;

use App\Shared\ActionProvider\ActionProvider;

trait WithActions
{
    protected ?ActionProvider $actionProvider = null;

    public function withActions(ActionProvider $provider): static
    {
        $this->actionProvider = $provider;
        return $this;
    }

    protected function actions(): array
    {
        if ($this->actionProvider) {
            $data = [];
            foreach ($this->items() as $item) {
                $data[] = $this->actionProvider->preparedActions($item);
            }
            return $data;
        } else {
            return [];
        }
    }
}
