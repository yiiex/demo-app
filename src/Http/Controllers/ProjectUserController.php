<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\Project;
use App\Models\ProjectUser;
use App\Policies\ProjectPolicy;
use App\Policies\ProjectUserPolicy;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Yiiex\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;

final class ProjectUserController
{

    public function create(
        #[ModelContext(Project::class, 'project', scenario: 'addUser', policy: ProjectPolicy::class)]
        Project               $project,
        #[ModelContext(ProjectUser::class, scenario: 'insert')]
        ProjectUser           $projectUser,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Add user')
            ->addBreadcrumb($project->name, ['project.show', ['project' => $project->id]])
            ->addBreadcrumb('Add user', ['project.user.create', ['project' => $project->id]]);

        return $inertia->render('ProjectUser/Form', [
            'form' => $formData->setModel($projectUser),
            'saveUrl' => $url->generate('project.user.store', ['project' => $project->id]),
        ]);
    }

    public function store(
        #[ModelContext(Project::class, 'project', scenario: 'addUser', policy: ProjectPolicy::class)]
        Project                $project,
        #[ModelContext(ProjectUser::class, scenario: 'insert')]
        ProjectUser            $projectUser,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        FormData               $form,
    ): ResponseInterface
    {
        $projectUser->project_id = $project->id;
        return $form
            ->setModel($projectUser)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(ProjectUser $projectUser) => [
                'success' => $projectUser->save(false),
                'message' => 'User added successfully.',
                'redirectUrl' => $url->generate('project.show', ['project' => $project->id]),
            ]);
    }

    public function destroy(
        #[ModelContext(ProjectUser::class, 'user', with: ['project_r'], scenario: 'delete', policy: ProjectUserPolicy::class)]
        ProjectUser           $projectUser,
        UrlGeneratorInterface $url,
        ResponseHelper        $response,
    ): ResponseInterface
    {
        return $response->json([
            'success' => $success = !!$projectUser->delete(),
            'message' => $success ? 'User deleted.' : 'User not deleted.',
            'redirectUrl' => $url->generate('project.show', ['project' => $projectUser->project_id]),
        ]);
    }
}
