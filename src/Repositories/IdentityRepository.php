<?php

namespace App\Repositories;

use App\Models\User;
use Yiisoft\Auth\IdentityInterface;
use Yiisoft\Auth\IdentityRepositoryInterface;

class IdentityRepository implements IdentityRepositoryInterface
{

    public function findIdentity(string $id): ?IdentityInterface
    {
        return User::model()->findByPk($id);
    }
}
