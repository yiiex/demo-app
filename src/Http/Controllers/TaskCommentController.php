<?php

namespace App\Http\Controllers;

use App\Http\Attributes\ModelContext;
use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ActionProviders\TaskCommentActions;
use App\Infrastructure\Form\FormData;
use App\Infrastructure\View\NavManager;
use App\Models\Task;
use App\Models\TaskComment;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\{ResponseInterface, ServerRequestInterface};
use Yii1x\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class TaskCommentController
{
    public function index(
        #[ModelContext(Task::class, 'task')]
        Task                   $task,
        ServerRequestInterface $request,
        ResponseHelper         $response,
        TaskCommentActions     $actions,
    ): ResponseInterface
    {
        $queryParams = $request->getQueryParams();

        $query = TaskComment::queryBuilder()
            ->with(['user_r'])
            ->where('task_id', $task->id)
            ->orderBy('t.id DESC');

        $dataProvider = new CrudDataProvider($query, 10, $queryParams['page'] ?? 1);
        $dataProvider->withActions($actions);

        return $response->json([
            'success' => true,
            'data' => $dataProvider,
        ]);
    }

    public function store(
        #[ModelContext(Task::class, 'task')]
        Task                   $task,
        #[ModelContext(TaskComment::class, scenario: 'insert')]
        TaskComment            $comment,
        ServerRequestInterface $request,
        CurrentUser            $user,
        FormData               $form,

    ): ResponseInterface
    {
        $comment->task_id = $task->id;
        $comment->user_id = $user->getId();

        return $form
            ->setModel($comment)
            ->setAttributes(array_merge($request->getParsedBody(), [
                'task_id' => $task->id,
                'user_id' => $user->getId(),
            ]))
            ->validateWithResponse(fn(TaskComment $entry) => [
                'success' => $entry->save(false),
                'message' => 'Comment added.',
            ]);
    }

    public function edit(
        #[ModelContext(TaskComment::class, 'comment', ['task_r'], scenario: 'update')]
        TaskComment           $comment,
        NavManager            $nav,
        Inertia               $inertia,
        UrlGeneratorInterface $url,
        FormData              $formData,
    ): ResponseInterface
    {
        $nav
            ->setTitle('Edit comment')
            ->addBreadcrumb($comment->task_r->reference, ['task.show', ['task' => $comment->task_r->id]])
            ->addBreadcrumb('Edit comment', ['task.edit', ['task' => $comment->task_r->id]]);
        return $inertia->render('TaskComment/Form', [
            'form' => $formData->setModel($comment),
            'saveUrl' => $url->generate('taskComment.update', ['comment' => $comment->id]),
        ]);
    }

    public function update(
        #[ModelContext(TaskComment::class, 'comment', ['task_r'], scenario: 'update')]
        TaskComment            $comment,
        ServerRequestInterface $request,
        UrlGeneratorInterface  $url,
        FormData           $form,
    ): ResponseInterface
    {
        return $form
            ->setModel($comment)
            ->setAttributes($request->getParsedBody())
            ->validateWithResponse(fn(TaskComment $model) => [
                'success' => $model->save(false),
                'message' => 'Comment updated.',
                'redirectUrl' => $url->generate('task.show', ['task' => $model->task_id]),
            ]);
    }


    public function destroy(
        #[ModelContext(TaskComment::class, 'comment')]
        TaskComment    $comment,
        ResponseHelper $response,
        CurrentUser    $user,
    ): ResponseInterface
    {
        if ($comment->user_id != $user->getId()) {
            return $response->error(403, 'Access denied');
        }

        return $response->json([
            'success' => $success = !!$comment->delete(),
            'message' => $success ? 'Comment deleted.' : 'Comment not deleted.',
        ]);
    }
}
