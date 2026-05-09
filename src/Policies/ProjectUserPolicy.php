<?php

namespace App\Policies;

use App\Models\ProjectUser;
use Yiisoft\User\CurrentUser;

final class ProjectUserPolicy extends BasePolicy
{
    public function insert(ProjectUser $projectUser, CurrentUser $user): bool
    {
        return $projectUser->isNewRecord
            && ($projectUser->project_r?->isOwner($user->getIdentity()) && $this->isAdmin($user));
    }

    public function delete(ProjectUser $projectUser, CurrentUser $user): bool
    {
        return !$projectUser->isNewRecord
            && ($projectUser->project_r?->isOwner($user->getIdentity()) && $this->isAdmin($user));
    }
}
