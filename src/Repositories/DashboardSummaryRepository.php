<?php

namespace App\Repositories;

use App\Models\{Task, TaskTimeEntry, User};
use Yii1x\ActiveRecord\{ConditionBuilder, QueryBuilder};

class DashboardSummaryRepository
{
    public function problemTaskData(?User $user = null): array
    {
        return [
            'overdue' => Task::queryBuilder()
                ->when($user, function(QueryBuilder $query, User $user) {
                    $query->whereRelation('user_links', fn(ConditionBuilder $cb) => $cb
                        ->where('user_id', $user->getId()));
                })
                ->where('t.status', '<>', Task::STATUS_DONE)
                ->where('t.deadline', '<', date('Y-m-d'))
                ->whereNotNull('t.deadline')
                ->count(),
        ];
    }

    public function taskData(?User $user = null): array
    {
        $query = Task::queryBuilder()->when($user, function(QueryBuilder $query, User $user) {
            $query->whereRelation('user_links', fn(ConditionBuilder $cb) => $cb->where('user_id', $user->getId()));
        });

        return [
            'total' => $query->fork()->count(),
            'completed' => $query->fork()->where('t.status', Task::STATUS_DONE)->count(),
            'inProgress' => $query->fork()->where('t.status', Task::STATUS_IN_PROGRESS)->count(),
        ];
    }

    public function timeData(?User $user = null): array
    {
        $query = TaskTimeEntry::queryBuilder()
            ->when($user, fn(QueryBuilder $query) => $query->where('user_id', $user->getId()));

        $week = $query->fork()
            ->scopes(['period' => ['week']])
            ->select(['SUM(t.duration) as duration', 't.user_id'])
            ->groupBy('t.user_id')->find();

        $today = $query->fork()
            ->scopes(['period' => ['today']])
            ->select(['SUM(t.duration) as duration', 't.user_id'])
            ->groupBy('t.user_id')->find();

        $total = $query->select(['SUM(t.duration) as duration', 't.user_id'])->groupBy('t.user_id')->find();
        return [
            'total' => $total->duration ?? '0h',
            'week' => $week->duration ?? '0h',
            'today' => $today->duration ?? '0h',
        ];
    }
}
