<?php

namespace App\Policies;

use App\Models\TaskComment;
use Yiisoft\User\CurrentUser;

final class TaskCommentPolicy extends BasePolicy
{
    public function update(TaskComment $comment, CurrentUser $user): bool
    {
        return !$comment->isNewRecord && ($comment->user_id == $user->getId() || $this->isAdmin($user));
    }

    public function delete(TaskComment $comment, CurrentUser $user): bool
    {
        return $this->update($comment, $user);
    }
}
