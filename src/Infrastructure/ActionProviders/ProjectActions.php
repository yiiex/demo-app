<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\Project;
use App\Models\User;
use App\Shared\ActionProvider\{Action, SimpleActionProvider};
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class ProjectActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser $user
    ) {}

    /**
     * @param Project $model
     * @return Action[]
     */
    public function actions($model): array
    {
        $canAdmin = fn(?Project $project, ?CurrentUser $user) => $user->getIdentity()?->can(User::ROLE_ADMIN) && !$project?->isNewRecord;
        return [
            Action::new('create')
                ->title('Create')
                ->url($this->urlGenerator->generate('project.create'))
                ->visible(fn(?Project $project, ?CurrentUser $user) =>
                    $user->getIdentity()?->can(User::ROLE_ADMIN) && $project?->isNewRecord
                ),

            Action::new('show')
                ->title('Show')
                ->url(fn(?Project $project) => $this->urlGenerator->generate('project.show', ['project' => $project?->id]))
                ->visible($canAdmin),

            Action::new('edit')
                ->title('Edit')
                ->url(fn(?Project $project) => $this->urlGenerator->generate('project.edit', ['project' => $project?->id]))
                ->visible($canAdmin),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?Project $project) => "Are you sure you want to delete project \"$project->name\"?")
                ->process('Deleting project, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?Project $project) => $this->urlGenerator->generate('project.destroy', ['project' => $project?->id]))
                ->visible($canAdmin),
        ];
    }
}
