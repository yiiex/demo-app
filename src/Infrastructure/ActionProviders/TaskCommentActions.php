<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\TaskComment;
use App\Models\User;
use App\Shared\ActionProvider\Action;
use App\Shared\ActionProvider\SimpleActionProvider;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class TaskCommentActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser           $user
    )
    {
    }

    /**
     * @param TaskComment $model
     * @return Action[]
     */
    public function actions($model): array
    {
        $isExisting = fn(?TaskComment $entry) => !$entry?->isNewRecord;
        $canEdit = fn(?TaskComment $entry, ?CurrentUser $user) => $isExisting($entry)
            && ($entry?->user_id == $user?->getId() || $user->getIdentity()->can(User::ROLE_ADMIN));

        return [
            Action::new('edit')
                ->title('Edit')
                ->url(fn(?TaskComment $entry) => $this->urlGenerator->generate('taskComment.edit', ['comment' => $entry?->id]))
                ->visible($canEdit),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?TaskComment $entry) => "Are you sure you want to delete this comment?")
                ->process('Deleting comment, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?TaskComment $entry) => $this->urlGenerator->generate('taskComment.destroy', ['comment' => $entry?->id]))
                ->visible($canEdit),
        ];
    }
}
