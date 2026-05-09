<?php

namespace App\Policies;

use App\Models\{Task, User};
use Yiisoft\User\CurrentUser;

final class TaskPolicy extends BasePolicy
{
    public function insert(Task $task, CurrentUser $user): bool
    {
        return $task->isNewRecord;
    }

    public function view(Task $task, CurrentUser $user): bool
    {
        return !$task->isNewRecord && ($task->isAssignee($user->getIdentity()) || $this->isAdmin($user));
    }

    public function take(Task $task, CurrentUser $user): bool
    {
        return !$task->isNewRecord && $task->status === Task::STATUS_PENDING && $task->isAssignee($user->getIdentity());
    }

    public function complete(Task $task, CurrentUser $user): bool
    {
        return $task->isAssignee($user->getIdentity())
            && $task->status === Task::STATUS_IN_PROGRESS;
    }

    public function return(Task $task, CurrentUser $user): bool
    {
        return $task->isAssignee($user->getIdentity()) && $task->status === Task::STATUS_DONE;
    }

    public function logWork(Task $task, CurrentUser $user): bool
    {
        return !$task->isNewRecord && $task->isAssignee($user->getIdentity());
    }

    public function update(Task $task, CurrentUser $user): bool
    {
        return !$task->isNewRecord && ($task->user_id == $user->getId() || $this->isAdmin($user));
    }

    public function delete(Task $task, CurrentUser $user): bool
    {
        return $this->update($task, $user);
    }
}
