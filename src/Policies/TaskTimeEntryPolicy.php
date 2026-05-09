<?php

namespace App\Policies;

use App\Models\TaskTimeEntry;
use Yiisoft\User\CurrentUser;

final class TaskTimeEntryPolicy extends BasePolicy
{
    public function update(TaskTimeEntry $entry, CurrentUser $user): bool
    {
        return !$entry->isNewRecord && ($entry->user_id == $user->getId() || $this->isAdmin($user));
    }

    public function delete(TaskTimeEntry $entry, CurrentUser $user): bool
    {
        return !$entry->isNewRecord && ($entry->user_id == $user->getId() || $this->isAdmin($user));
    }
}
