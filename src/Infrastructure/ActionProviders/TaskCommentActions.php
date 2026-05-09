<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\TaskComment;
use App\Models\User;
use App\Policies\TaskCommentPolicy;
use App\Shared\ActionProvider\Action;
use App\Shared\ActionProvider\SimpleActionProvider;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class TaskCommentActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser           $user,
        protected TaskCommentPolicy     $policy,
    )
    {
    }

    /**
     * @param TaskComment $model
     * @return Action[]
     */
    public function actions($model): array
    {
        return [
            Action::new('edit')
                ->title('Edit')
                ->url(fn(?TaskComment $entry) => $this->urlGenerator->generate('taskComment.edit', ['comment' => $entry?->id]))
                ->visible(fn() => $this->policy->update($model, $this->user)),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?TaskComment $entry) => "Are you sure you want to delete this comment?")
                ->process('Deleting comment, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?TaskComment $entry) => $this->urlGenerator->generate('taskComment.destroy', ['comment' => $entry?->id]))
                ->visible(fn() => $this->policy->delete($model, $this->user)),
        ];
    }
}
