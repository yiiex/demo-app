<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ActionProviders\TaskTimeEntryActions;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\Task;
use App\Models\TaskTimeEntry;
use App\Policies\TaskPolicy;
use App\Policies\TaskTimeEntryPolicy;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Yiiex\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class TaskTimeEntryController
{
    public function index(
        #[ModelContext(Task::class, 'task', scenario: 'view', policy: TaskPolicy::class)]
        Task                   $task,
        ServerRequestInterface $request,
        ResponseHelper         $response,
        TaskTimeEntryActions   $actions,
    ): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $query = TaskTimeEntry::queryBuilder()
            ->with(['user'])
            ->where('task_id', $task->id)
            ->orderBy('t.id DESC');

        $dataProvider = new CrudDataProvider($query, 10, $queryParams['page'] ?? 1);
        $dataProvider->withActions($actions);

        return $response->json([
            'success' => true,
            'data' => $dataProvider,
        ]);
    }

    public function create(
        #[ModelContext(Task::class, 'task', scenario: 'logWork', policy: TaskPolicy::class)]
        Task                  $task,
        #[ModelContext(TaskTimeEntry::class, scenario: 'insert')]
        TaskTimeEntry         $entry,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
        NavManager            $nav,
    ): ResponseInterface
    {
        $nav
            ->setTitle($task->reference)
            ->addBreadcrumb($task->reference, ['task.show', ['task' => $task->id]])
            ->addBreadcrumb('Log work', ['taskTimeEntry.create', ['task' => $task->id]]);

        return $inertia->render('TaskTimeEntry/Form', [
            'form' => $formData->setModel($entry),
            'saveUrl' => $url->generate('taskTimeEntry.store', ['task' => $task->id]),
        ]);
    }

    public function store(
        #[ModelContext(Task::class, 'task', scenario: 'logWork', policy: TaskPolicy::class)]
        Task                   $task,
        #[ModelContext(TaskTimeEntry::class, scenario: 'insert')]
        TaskTimeEntry          $entry,
        ServerRequestInterface $request,
        CurrentUser            $user,
        UrlGeneratorInterface  $url,
        FormData               $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($entry)
            ->setAttributes(array_merge($request->getParsedBody(), [
                'task_id' => $task->id,
                'user_id' => $user->getId(),
            ]))
            ->validateWithResponse(fn(TaskTimeEntry $entry) => [
                'success' => $entry->save(false),
                'message' => 'Time entry created.',
                'redirectUrl' => $url->generate('task.show', ['task' => $task->id]),
            ]);
    }

    public function edit(
        #[ModelContext(TaskTimeEntry::class, 'time', scenario: 'update', policy: TaskTimeEntryPolicy::class)]
        TaskTimeEntry         $entry,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
        NavManager            $nav,
    ): ResponseInterface
    {
        $nav
            ->setTitle($entry->task->reference)
            ->addBreadcrumb($entry->task->reference, ['task.show', ['task' => $entry->task_id]])
            ->addBreadcrumb('Edit time entry', ['taskTimeEntry.create', ['task' => $entry->task_id]]);

        return $inertia->render('TaskTimeEntry/Form', [
            'form' => $formData->setModel($entry),
            'saveUrl' => $url->generate('taskTimeEntry.update', ['time' => $entry->id]),
        ]);
    }

    public function update(
        #[ModelContext(TaskTimeEntry::class, 'time', scenario: 'update', policy: TaskTimeEntryPolicy::class)]
        TaskTimeEntry          $entry,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        FormData               $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($entry)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(TaskTimeEntry $entry) => [
                'success' => $entry->save(false),
                'message' => 'Time entry updated.',
                'redirectUrl' => $url->generate('task.show', ['task' => $entry->task->id]),
            ]);
    }

    public function destroy(
        #[ModelContext(TaskTimeEntry::class, 'time', scenario: 'delete', policy: TaskTimeEntryPolicy::class)]
        TaskTimeEntry         $entry,
        ResponseHelper        $response,
        UrlGeneratorInterface $url,
    ): ResponseInterface
    {
        return $response->json([
            'success' => $success = !!$entry->delete(),
            'message' => $success ? 'Time entry deleted.' : 'Time entry not deleted.',
        ]);
    }
}
