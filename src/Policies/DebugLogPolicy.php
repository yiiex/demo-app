<?php

namespace App\Policies;

use App\Models\DebugLog;
use Yiisoft\User\CurrentUser;

final class DebugLogPolicy extends BasePolicy
{
    public function view(DebugLog $model, CurrentUser $user): bool
    {
        return $this->isAdmin($user);
    }
}
