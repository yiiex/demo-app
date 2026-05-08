<?php

namespace App\Http\Controllers;

use App\Infrastructure\View\NavManager;
use Yii1x\Inertia\Inertia;

final class DashboardController
{
    public function index(NavManager $navManager, Inertia $inertia): \Psr\Http\Message\ResponseInterface
    {
        $navManager->setTitle('Dashboard');
        return $inertia->render('Dashboard/Index');
    }
}
