<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface;
use Yii1x\ActiveRecord\Db\Schema\DbCriteria;
use Yii1x\Inertia\Inertia;

final readonly class MainController
{
    public function index(Inertia $inertia): ResponseInterface
    {
        $users = User::model()->findAll();

        return $inertia->render('Index', [
            'users' => $users,
        ]);
    }
}
