<?php

namespace App\Infrastructure\ActionProviders;

use App\Models\User;
use App\Shared\ActionProvider\{Action, SimpleActionProvider};
use App\Policies\UserPolicy;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

class UserActions extends SimpleActionProvider
{
    public function __construct(
        protected UrlGeneratorInterface $urlGenerator,
        protected CurrentUser           $user,
        protected UserPolicy            $policy,
    )
    {
    }

    /**
     * @param User $model
     * @return Action[]
     */
    public function actions($model): array
    {
        return [
            Action::new('create')
                ->title('Create')
                ->url($this->urlGenerator->generate('user.create'))
                ->visible(fn() => $this->policy->insert($model, $this->user)),

            Action::new('show')
                ->title('Show')
                ->url(fn(?User $user) => $this->urlGenerator->generate('user.show', ['user' => $user?->id]))
                ->visible(fn() => $this->policy->view($model, $this->user)),

            Action::new('edit')
                ->title('Edit')
                ->url(fn(?User $user) => $this->urlGenerator->generate('user.edit', ['user' => $user?->id]))
                ->visible(fn() => $this->policy->update($model, $this->user)),

            Action::new('destroy')
                ->title('Delete')
                ->method('DELETE')
                ->confirm(fn(?User $user) => "Are you sure you want to delete user \"$user->fullName\"?")
                ->process('Deleting user, please wait...')
                ->async()
                ->variant('destructive')
                ->url(fn(?User $user) => $this->urlGenerator->generate('user.destroy', ['user' => $user?->id]))
                ->visible(fn() => $this->policy->delete($model, $this->user)),
        ];
    }
}
