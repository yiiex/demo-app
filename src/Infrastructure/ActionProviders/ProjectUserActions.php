<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\ProjectUser;
use App\Shared\ActionProvider\{Action, SimpleActionProvider};
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class ProjectUserActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser $user
    ) {}

    /**
     * @param ProjectUser $model
     * @return Action[]
     */
    public function actions($model): array
    {
        return [
            Action::new('create')
                ->title('Add User')
                ->variant('outline')
                ->url(fn(?ProjectUser $projectUser) => $this->urlGenerator->generate('project.user.create', ['project' => $projectUser?->project_r?->id]))
                ->visible(fn(?ProjectUser $projectUser, ?CurrentUser $user) =>
                    $projectUser?->project_r?->isOwner($user->getIdentity()) && $projectUser?->isNewRecord
                ),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?ProjectUser $projectUser) => "Are you sure you want to delete user?")
                ->process('Deleting user, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?ProjectUser $projectUser) => $this->urlGenerator->generate('project.user.destroy', ['user' => $projectUser?->id]))
                ->visible(fn(?ProjectUser $projectUser, ?CurrentUser $user) =>
                    $projectUser?->project_r?->isOwner($user->getIdentity()) && !$projectUser?->isNewRecord
                ),
        ];
    }
}
