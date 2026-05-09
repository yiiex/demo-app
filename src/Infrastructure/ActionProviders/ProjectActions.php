<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\Project;
use App\Models\User;
use App\Shared\ActionProvider\{Action, SimpleActionProvider};
use App\Policies\ProjectPolicy;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class ProjectActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser $user,
        protected ProjectPolicy $policy,
    ) {}

    /**
     * @param Project $model
     * @return Action[]
     */
    public function actions($model): array
    {
        return [
            Action::new('create')
                ->title('Create')
                ->url($this->urlGenerator->generate('project.create'))
                ->visible(fn() => $this->policy->insert($model, $this->user)),

            Action::new('show')
                ->title('Show')
                ->url(fn(?Project $project) => $this->urlGenerator->generate('project.show', ['project' => $project?->id]))
                ->visible(fn() => $this->policy->view($model, $this->user)),

            Action::new('edit')
                ->title('Edit')
                ->url(fn(?Project $project) => $this->urlGenerator->generate('project.edit', ['project' => $project?->id]))
                ->visible(fn() => $this->policy->update($model, $this->user)),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?Project $project) => "Are you sure you want to delete project \"$project->name\"?")
                ->process('Deleting project, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?Project $project) => $this->urlGenerator->generate('project.destroy', ['project' => $project?->id]))
                ->visible(fn() => $this->policy->delete($model, $this->user)),
        ];
    }
}
