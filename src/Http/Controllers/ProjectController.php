<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Policies\ProjectPolicy;
use App\Infrastructure\ActionProviders\{ProjectActions, ProjectUserActions};
use App\Infrastructure\Filter\ProjectFilter;
use App\Infrastructure\Filter\ProjectUserFilter;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Yiiex\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class ProjectController
{
    public function __construct(NavManager $nav)
    {
        $nav->addBreadcrumb('Projects', ['project.index']);
    }

    public function index(
        NavManager             $nav,
        Inertia                $inertia,
        ServerRequestInterface $request,
        ProjectActions         $actions,
        ProjectFilter          $filter,
    ): ResponseInterface
    {
        $nav->setTitle('Projects');
        $queryParams = $request->getQueryParams();
        $filter->setAttributes($queryParams);

        $dataProvider = new CrudDataProvider(Project::queryBuilder(), 10, $queryParams['page'] ?? 1);
        $dataProvider
            ->withActions($actions)
            ->withFilter($filter);

        return $inertia->render('Project/Index', [
            'dataProvider' => $dataProvider,
            'actions' => $actions->preparedActions(new Project('filter')),
        ]);
    }

    public function show(
        #[ModelContext(Project::class, 'project', scenario: 'view', policy: ProjectPolicy::class)]
        Project                $project,
        Inertia                $inertia,
        NavManager             $nav,
        ProjectUserActions     $userActions,
        ProjectUserFilter      $userFilter,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $urlGenerator,
    ): \Psr\Http\Message\ResponseInterface
    {
        $nav
            ->setTitle($project->name)
            ->addBreadcrumb($project->name, ['project.show', ['project' => $project->id]]);

        $queryParams = $request->getQueryParams();
        $userFilter->setAttributes($queryParams);

        $query = ProjectUser::queryBuilder()->where('t.project_id', $project->id);
        $dataProvider = new CrudDataProvider($query, 10, $queryParams['page'] ?? 1);
        $dataProvider
            ->withActions($userActions)
            ->withFilter($userFilter);

        $projectUser = new ProjectUser('filter');
        $projectUser->project_r = $project;
        return $inertia->render('Project/Show', [
            'project' => $project,
            'dataProvider' => $dataProvider,
            'userActions' => $userActions->preparedActions($projectUser),
            'currentUrl' => $urlGenerator->generate('project.show', ['project' => $project->id]),
        ]);
    }

    public function create(
        #[ModelContext(Project::class, 'project', scenario: 'insert', policy: ProjectPolicy::class)]
        Project               $project,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Projects')
            ->addBreadcrumb('Create', ['project.create']);

        return $inertia->render('Project/Form', [
            'form' => $formData->setModel($project),
            'saveUrl' => $url->generate('project.store'),
        ]);
    }

    public function store(
        #[ModelContext(Project::class, scenario: 'insert', policy: ProjectPolicy::class)]
        Project                $project,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        CurrentUser            $user,
        FormData               $form,
    ): ResponseInterface
    {
        $project->user_id = $user->getId();
        return $form
            ->setModel($project)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(function ($model) use ($url) {
                return [
                    'success' => $model->save(false),
                    'message' => 'Project created.',
                    'redirectUrl' => $url->generate('project.index'),
                ];
            });
    }

    public function edit(
        #[ModelContext(Project::class, 'project', scenario: 'update', policy: ProjectPolicy::class)]
        Project               $project,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Edit ' . ($project->name ?? 'Project'))
            ->addBreadcrumb('Edit', ['project.edit', ['project' => $project->id]]);

        return $inertia->render('Project/Form', [
            'form' => $formData->setModel($project),
            'saveUrl' => $url->generate('project.update', ['project' => $project->id]),
        ]);
    }

    public function update(
        #[ModelContext(Project::class, 'project', scenario: 'update', policy: ProjectPolicy::class)]
        Project                $project,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        FormData               $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($project)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(function ($model) use ($url) {
                return [
                    'success' => $model->save(false),
                    'message' => 'Project updated.',
                    'redirectUrl' => $url->generate('project.index'),
                ];
            });
    }

    public function destroy(
        #[ModelContext(Project::class, 'project', scenario: 'delete', policy: ProjectPolicy::class)]
        Project               $project,
        UrlGeneratorInterface $url,
        ResponseHelper        $response,
    ): ResponseInterface
    {
        $success = $project->delete();

        return $response->json([
            'success' => $success,
            'message' => $success ? 'Project deleted.' : 'Project not deleted.',
            'redirectUrl' => $url->generate('project.index'),
        ]);
    }
}
