<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ResponseHelper;
use App\Infrastructure\ActionProviders\TaskActions;
use App\Infrastructure\Filter\TimeSheetFilter;
use App\Infrastructure\View\NavManager;
use App\Models\Task;
use App\Models\TaskTimeEntry;
use App\Shared\DataProvider\CrudDataProvider;
use Psr\Http\Message\ServerRequestInterface;
use Yii1x\ActiveRecord\ConditionBuilder;
use Yiiex\Inertia\Inertia;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\User\CurrentUser;

final class TimeSheetController
{
    public function index(
        CurrentUser            $user,
        Inertia                $inertia,
        ServerRequestInterface $request,
        TaskActions            $actions,
        NavManager             $nav,
        TimeSheetFilter        $filter,
        UrlGeneratorInterface  $url,
    ): \Psr\Http\Message\ResponseInterface
    {

        $queryParams = $request->getQueryParams();
        $date = $queryParams['date'] ?? date('Y-m-d');

        $filter->setAttributes(compact('date'));
        if (!$filter->validate()) {
            $date = date('Y-m-d');
            $filter->setAttributes(compact('date'));
        }
        $nav->setTitle('Timesheet')
            ->addBreadcrumbRoute('Timesheet', 'timeSheet.index');

        $time = TaskTimeEntry::queryBuilder()
            ->select(['SUM(t.duration) as duration', 't.user_id', 't.date'])
            ->where('t.user_id', $user->getId())
            ->where(function (ConditionBuilder $query) use ($date) {
                $query
                    ->where('t.date', '>=', date('Y-m-01', strtotime($date)))
                    ->where('t.date', '<=', date('Y-m-t', strtotime($date)));
            })
            ->groupBy(['t.user_id', 't.date'])
            ->findAll();

        $taskDataProvider = new CrudDataProvider(Task::queryBuilder(), 20, $queryParams['page'] ?? 1)
            ->withActions($actions->only(['show']))
            ->withFilter($filter);

        return $inertia->render('TimeSheet/Index', [
            'currentUrl' => $url->generate('timeSheet.index'),
            'date' => $date,
            'timeEntries' => $time,
            'taskDataProvider' => $taskDataProvider,
        ]);
    }
}
