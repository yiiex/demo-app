<?php

namespace App\Policies;

use App\Models\User;
use Yiisoft\User\CurrentUser;

final class UserPolicy extends BasePolicy
{
    public function insert(User $user, CurrentUser $currentUser): bool
    {
        return $this->isAdmin($currentUser) && $user->isNewRecord;
    }

    public function view(User $user, CurrentUser $currentUser): bool
    {
        return !$user->isNewRecord;
    }

    public function update(User $user, CurrentUser $currentUser): bool
    {
        return $this->isAdmin($currentUser) && !$user->isNewRecord;
    }

    public function delete(User $user, CurrentUser $currentUser): bool
    {
        return $this->isAdmin($currentUser) && !$user->isNewRecord;
    }
}
