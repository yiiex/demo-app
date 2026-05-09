<?php

namespace App\Policies;

use App\Models\Project;
use Yiisoft\User\CurrentUser;

final class ProjectPolicy extends BasePolicy
{
    public function insert(Project $project, CurrentUser $user): bool
    {
        return $project->isNewRecord;
    }

    public function view(Project $project, CurrentUser $user): bool
    {
        return !$project->isNewRecord && ($project->isOwner($user->getIdentity()) || $project->isMember($user->getIdentity()) || $this->isAdmin($user));
    }

    public function update(Project $project, CurrentUser $user): bool
    {
        return ($this->isAdmin($user) || $project->isOwner($user->getIdentity())) && !$project->isNewRecord;
    }

    public function addUser(Project $project, CurrentUser $user): bool
    {
        return $this->update($project, $user);
    }

    public function delete(Project $project, CurrentUser $user): bool
    {
        return $this->update($project, $user);
    }
}
