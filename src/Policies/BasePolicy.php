<?php

namespace App\Policies;

use App\Models\User;
use Yiisoft\User\CurrentUser;

abstract class BasePolicy implements Policy
{
    protected function isAdmin(CurrentUser $user): bool
    {
        return $user->getIdentity()?->can(User::ROLE_ADMIN) ?? false;
    }
}
