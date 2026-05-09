<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\TaskTimeEntry;
use App\Policies\TaskTimeEntryPolicy;
use App\Shared\ActionProvider\Action;
use App\Shared\ActionProvider\SimpleActionProvider;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class TaskTimeEntryActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser           $user,
        protected TaskTimeEntryPolicy   $policy,
    )
    {
    }

    /**
     * @param TaskTimeEntry $model
     * @return Action[]
     */
    public function actions($model): array
    {
        return [
            Action::new('edit')
                ->title('Edit')
                ->url(fn(?TaskTimeEntry $entry) => $this->urlGenerator->generate('taskTimeEntry.edit', ['time' => $entry?->id]))
                ->visible(fn() => $this->policy->update($model, $this->user)),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?TaskTimeEntry $entry) => "Are you sure you want to delete this time entry?")
                ->process('Deleting time entry, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?TaskTimeEntry $entry) => $this->urlGenerator->generate('taskTimeEntry.destroy', ['time' => $entry?->id]))
                ->visible(fn() => $this->policy->delete($model, $this->user)),
        ];
    }
}
