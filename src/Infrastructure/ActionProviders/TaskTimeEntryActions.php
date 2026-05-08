<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\TaskTimeEntry;
use App\Shared\ActionProvider\Action;
use App\Shared\ActionProvider\SimpleActionProvider;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class TaskTimeEntryActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser $user
    ) {}

    /**
     * @param TaskTimeEntry $model
     * @return Action[]
     */
    public function actions($model): array
    {
        $isExisting = fn(?TaskTimeEntry $entry) => !$entry?->isNewRecord;
        $isOwner = fn(?TaskTimeEntry $entry, ?CurrentUser $user) => $isExisting($entry) && $entry?->user_id == $user?->getId();

        return [
            Action::new('edit')
                ->title('Edit')
                ->url(fn(?TaskTimeEntry $entry) => $this->urlGenerator->generate('taskTimeEntry.edit', ['time' => $entry?->id]))
                ->visible($isOwner),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?TaskTimeEntry $entry) => "Are you sure you want to delete this time entry?")
                ->process('Deleting time entry, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?TaskTimeEntry $entry) => $this->urlGenerator->generate('taskTimeEntry.destroy', ['time' => $entry?->id]))
                ->visible($isOwner),
        ];
    }
}
