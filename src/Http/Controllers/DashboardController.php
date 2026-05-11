<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ActionProviders\TaskActions;
use App\Infrastructure\Filter\TaskFilter;
use App\Infrastructure\View\NavManager;
use App\Models\Task;
use App\Repositories\DashboardSummaryRepository;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\ServerRequestInterface;
use Yiiex\Inertia\Inertia;
use Yiisoft\User\CurrentUser;

final class DashboardController
{
    public function index(
        NavManager                 $navManager,
        Inertia                    $inertia,
        CurrentUser                $user,
        DashboardSummaryRepository $repository,
    ): \Psr\Http\Message\ResponseInterface
    {
        $navManager->setTitle('Dashboard');
        $user = $user->getIdentity();
        return $inertia->render('Dashboard/Index', [
            'taskSummary' => fn() => $repository->taskData($user),
            'timeSummary' => fn() => $repository->timeData($user),
            'problemTaskSummary' => fn() => $repository->problemTaskData($user),
        ]);
    }

    public function tasks(
        ServerRequestInterface $request,
        TaskActions            $actions,
        TaskFilter             $filter,
        ResponseHelper         $response,
    ): \Psr\Http\Message\ResponseInterface
    {
        $queryParams = $request->getQueryParams();
        $filter->setAttributes($queryParams + ['myTasks' => true]);
        $query = Task::queryBuilder()->orderBy('t.updated_at DESC');
        $dataProvider = new CrudDataProvider($query, 10, $queryParams['page'] ?? 1);
        $dataProvider
            ->withActions($actions)
            ->withFilter($filter);
        return $response->json([
            'data' => $dataProvider,
        ]);
    }
}
