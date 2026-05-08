<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\Project;
use App\Models\ProjectUser;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Yii1x\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class ProjectUserController
{

    public function create(
        #[ModelContext(Project::class, 'project', scenario: 'insert')]
        Project               $project,
        #[ModelContext(ProjectUser::class, scenario: 'insert')]
        ProjectUser           $projectUser,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        ResponseHelper        $response,
        FormData              $formData,
        CurrentUser           $user,
    ): ResponseInterface
    {
        if (!$project->isOwner($user->getIdentity())) {
            return $response->error(403, 'Access denied');
        }

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
        #[ModelContext(Project::class, 'project', scenario: 'insert')]
        Project                $project,
        #[ModelContext(ProjectUser::class, scenario: 'insert')]
        ProjectUser            $projectUser,
        ServerRequestInterface $request,
        ResponseHelper         $response,
        UrlGeneratorInterface  $url,
        CurrentUser            $user,
        FormData               $form,
    ): ResponseInterface
    {
        if (!$project->isOwner($user->getIdentity())) {
            return $response->error(403, 'Access denied');
        }
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
        #[ModelContext(ProjectUser::class, 'user', with: ['project_r'])]
        ProjectUser           $projectUser,
        UrlGeneratorInterface $url,
        ResponseHelper        $response,
        CurrentUser           $user,
    ): ResponseInterface
    {
        if (!$projectUser->project_r?->isOwner($user->getIdentity())) {
            return $response->error(403, 'Access denied');
        }
        return $response->json([
            'success' => $success = !!$projectUser->delete(),
            'message' => $success ? 'User deleted.' : 'User not deleted.',
            'redirectUrl' => $url->generate('project.show', ['project' => $projectUser->project_id]),
        ]);
    }
}
