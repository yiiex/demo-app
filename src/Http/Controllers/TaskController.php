<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ActionProviders\TaskActions;
use App\Infrastructure\Filter\TaskFilter;
use App\Infrastructure\Form\{FormData, TaskFormData};
use App\Infrastructure\View\NavManager;
use App\Models\{Task, TaskComment};
use App\Policies\TaskPolicy;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiiex\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;

final class TaskController
{
    public function __construct(NavManager $nav)
    {
        $nav->addBreadcrumb('Tasks', ['task.index']);
    }

    public function index(
        NavManager             $nav,
        Inertia                $inertia,
        ServerRequestInterface $request,
        TaskActions            $actions,
        TaskFilter             $filter,
    ): \Psr\Http\Message\ResponseInterface
    {
        $nav->setTitle('Tasks');
        $queryParams = $request->getQueryParams();
        $filter->setAttributes($queryParams);
        $query = Task::queryBuilder()->orderBy('t.updated_at DESC');
        $dataProvider = new CrudDataProvider($query, 10, $queryParams['page'] ?? 1);
        $dataProvider
            ->withActions($actions)
            ->withFilter($filter);
        return $inertia->render('Task/Index', [
            'dataProvider' => $dataProvider,
            'actions' => $actions->preparedActions(new Task('filter')),
        ]);
    }

    public function show(
        #[ModelContext(Task::class, 'task', [
            'project', 'user_links' => ['with' => ['time_summary', 'user']], 'spent_time',
        ], scenario: 'view', policy: TaskPolicy::class)]
        Task                  $task,
        Inertia               $inertia,
        NavManager            $nav,
        TaskActions           $actions,
        UrlGeneratorInterface $url,
        #[ModelContext(TaskComment::class, scenario: 'insert')]
        TaskComment           $comment,
        FormData              $commentForm,
    ): \Psr\Http\Message\ResponseInterface
    {
        $nav
            ->setTitle($task->reference)
            ->addBreadcrumb($task->reference, ['task.show', ['task' => $task->id]]);

        return $inertia->render('Task/Show', [
            'task' => $task,
            'actions' => $actions->preparedActions($task, ['show']),
            'timeEntryUrl' => $url->generate('taskTimeEntry.index', ['task' => $task->id]),
            'commentForm' => $commentForm->setModel($comment),
            'commentSaveUrl' => $url->generate('taskComment.store', ['task' => $task->id]),
            'commentDataUrl' => $url->generate('taskComment.index', ['task' => $task->id]),
        ]);
    }

    public function create(
        #[ModelContext(Task::class, 'task', scenario: 'insert', policy: TaskPolicy::class)]
        Task                  $task,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        TaskFormData          $formData,
    ): \Psr\Http\Message\ResponseInterface
    {
        $nav
            ->setTitle('Tasks')
            ->addBreadcrumb('Create', ['task.create']);
        return $inertia->render('Task/Form', [
            'form' => $formData->setModel($task),
            'saveUrl' => $url->generate('task.store'),
        ]);
    }

    public function store(
        #[ModelContext(Task::class, 'task', scenario: 'insert', policy: TaskPolicy::class)]
        Task                   $task,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        TaskFormData           $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($task)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(Task $model) => [
                'success' => $model->save(false),
                'message' => 'Task created.',
                'redirectUrl' => $url->generate('task.index'),
            ]);
    }

    public function edit(
        #[ModelContext(Task::class, 'task', scenario: 'update', policy: TaskPolicy::class)]
        Task                  $task,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        TaskFormData          $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Edit ' . $task->name)
            ->addBreadcrumb($task->reference, ['task.show', ['task' => $task->id]])
            ->addBreadcrumb('Edit', ['task.edit', ['task' => $task->id]]);
        return $inertia->render('Task/Form', [
            'form' => $formData->setModel($task),
            'saveUrl' => $url->generate('task.update', ['task' => $task->id]),
        ]);
    }

    public function update(
        #[ModelContext(Task::class, 'task', scenario: 'update', policy: TaskPolicy::class)]
        Task                   $task,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        TaskFormData           $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($task)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(Task $model) => [
                'success' => $model->save(false),
                'message' => 'Task updated.',
                'redirectUrl' => $url->generate('task.show', ['task' => $model->id]),
            ]);
    }

    private function changeStatus(
        Task                  $task,
        ResponseHelper        $response,
        UrlGeneratorInterface $url,
    ): ResponseInterface
    {
        return $response->json([
            'success' => $task->save(),
            'message' => 'Task status updated.',
            'redirectUrl' => $url->generate('task.show', ['task' => $task->id]),
        ]);
    }

    public function take(
        #[ModelContext(Task::class, 'task', scenario: 'take', policy: TaskPolicy::class)]
        Task                  $task,
        ResponseHelper        $response,
        UrlGeneratorInterface $url,
    ): ResponseInterface
    {
        return $this->changeStatus($task, $response, $url);
    }

    public function complete(
        #[ModelContext(Task::class, 'task', scenario: 'complete', policy: TaskPolicy::class)]
        Task                  $task,
        ResponseHelper        $response,
        UrlGeneratorInterface $url,
    ): ResponseInterface
    {
        return $this->changeStatus($task, $response, $url);
    }

    public function return(
        #[ModelContext(Task::class, 'task', scenario: 'return', policy: TaskPolicy::class)]
        Task                  $task,
        ResponseHelper        $response,
        UrlGeneratorInterface $url,
    ): ResponseInterface
    {
        return $this->changeStatus($task, $response, $url);
    }

    public function destroy(
        #[ModelContext(Task::class, 'task', scenario: 'delete', policy: TaskPolicy::class)]
        Task                  $task,
        UrlGeneratorInterface $url,
        ResponseHelper        $response,
    ): ResponseInterface
    {
        return $response->json([
            'success' => $success = !!$task->delete(),
            'message' => $success ? 'Task deleted.' : 'Task not deleted.',
            'redirectUrl' => $url->generate('task.index'),
        ]);
    }
}
