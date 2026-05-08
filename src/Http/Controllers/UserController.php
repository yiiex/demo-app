<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ActionProviders\UserActions;
use App\Infrastructure\Filter\UserFilter;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\User;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yii1x\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;

final class UserController
{
    public function __construct(NavManager $nav)
    {
        $nav->addBreadcrumb('Users', ['user.index']);
    }

    public function index(
        NavManager             $nav,
        Inertia                $inertia,
        ServerRequestInterface $request,
        UserActions            $actions,
        UserFilter             $filter,
    ): ResponseInterface
    {
        $nav->setTitle('Users');
        $queryParams = $request->getQueryParams();
        $filter->setAttributes($queryParams);
        $dataProvider = new CrudDataProvider(User::queryBuilder(), 10, $queryParams['page'] ?? 1);
        $dataProvider
            ->withActions($actions)
            ->withFilter($filter);
        return $inertia->render('User/Index', [
            'users' => $dataProvider,
            'actions' => $actions->preparedActions(new User('filter')),
        ]);
    }

    public function show(
        #[ModelContext(User::class, 'user')]
        User       $user,
        Inertia    $inertia,
        NavManager $nav,
    ): ResponseInterface
    {
        $nav
            ->setTitle($user->fullName)
            ->addBreadcrumb($user->fullName, ['user.show', ['user' => $user->id]]);

        return $inertia->render('User/Show', [
            'user' => $user,
        ]);
    }

    public function create(
        #[ModelContext(User::class, scenario: 'insert')]
        User                  $user,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Users')
            ->addBreadcrumb('Create', ['user.create']);
        return $inertia->render('User/Form', [
            'form' => $formData->setModel($user),
            'saveUrl' => $url->generate('user.store'),
        ]);
    }

    public function store(
        #[ModelContext(User::class, scenario: 'insert')]
        User                   $user,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        FormData               $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($user)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(User $model) => [
                'success' => $model->withHashedPassword()->save(false),
                'message' => 'User created.',
                'redirectUrl' => $url->generate('user.index'),
            ]);
    }

    public function edit(
        #[ModelContext(User::class, 'user', scenario: 'update')]
        User                  $user,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Edit ' . $user->fullName)
            ->addBreadcrumb('Edit', ['user.edit', ['user' => $user->id]]);
        return $inertia->render('User/Form', [
            'form' => $formData->setModel($user),
            'saveUrl' => $url->generate('user.update', ['user' => $user->id]),
        ]);
    }

    public function update(
        #[ModelContext(User::class, 'user', scenario: 'update')]
        User                   $user,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        FormData               $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($user)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(User $model) => [
                'success' => $model->save(false),
                'message' => 'User updated.',
                'redirectUrl' => $url->generate('user.index'),
            ]);
    }

    public function destroy(
        #[ModelContext(User::class, 'user')]
        User                  $user,
        UrlGeneratorInterface $url,
        ResponseHelper        $response,
    ): ResponseInterface
    {
        return $response->json([
            'success' => $success = !!$user->delete(),
            'message' => $success ? 'User deleted.' : 'User not deleted.',
            'redirectUrl' => $url->generate('user.index'),
        ]);
    }
}
