<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\Task;
use App\Shared\ActionProvider\{Action, SimpleActionProvider};
use App\Policies\TaskPolicy;
use App\Policies\UserPolicy;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class TaskActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser           $user,
        protected TaskPolicy            $policy,
    )
    {
    }

    /**
     * @param Task $model
     * @return Action[]
     */
    public function actions($model): array
    {
        return [
            Action::new('create')
                ->title('Create')
                ->url($this->urlGenerator->generate('task.create'))
                ->visible(fn() => $this->policy->insert($model, $this->user)),

            Action::new('show')
                ->title('Show')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('task.show', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->view($model, $this->user)),

            Action::new('take')
                ->title('Take in Work')
                ->method('POST')
                ->confirm(fn(?Task $task) => "Take task \"$task->name\" into work?")
                ->process('Taking task, please wait...')
                ->async()
                ->variant('outline')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('task.take', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->take($model, $this->user)),

            Action::new('complete')
                ->title('Complete')
                ->method('POST')
                ->confirm(fn(?Task $task) => "Mark task \"$task->name\" as completed?")
                ->process('Completing task, please wait...')
                ->async()
                ->variant('outline')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('task.complete', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->complete($model, $this->user)),

            Action::new('return')
                ->title('Return to Work')
                ->method('POST')
                ->confirm(fn(?Task $task) => "Return task \"$task->name\" back to pending?")
                ->process('Returning task, please wait...')
                ->async()
                ->variant('outline')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('task.return', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->return($model, $this->user)),

            Action::new('log_work')
                ->title('Log Work')
                ->variant('outline')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('taskTimeEntry.create', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->logWork($model, $this->user)),

            Action::new('edit')
                ->title('Edit')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('task.edit', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->update($model, $this->user)),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?Task $task) => "Are you sure you want to delete task \"$task->name\"?")
                ->process('Deleting task, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?Task $task) => $this->urlGenerator->generate('task.destroy', ['task' => $task?->id]))
                ->visible(fn() => $this->policy->delete($model, $this->user)),
        ];
    }
}
