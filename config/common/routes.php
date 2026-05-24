<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\ListManagerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectUserController;
use App\Http\Controllers\TaskCommentController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskTimeEntryController;
use App\Http\Controllers\TimeSheetController;
use App\Http\Controllers\UserController;
use App\Infrastructure\Middlewares\AdminMiddleware;
use Yiisoft\Auth\Middleware\Authentication;
use Yiisoft\Router\{Group, Route};

return [
    Group::create('/debug')->middleware(AdminMiddleware::class)->routes(
        Route::get('/show/{debug}')->action([DebugController::class, 'show'])->name('debug.show'),
    ),
    Group::create('/user')->routes(
        Group::create()->middleware(Authentication::class)->routes(
            Route::post('/logout')->action([AuthController::class, 'logout'])->name('user.logout'),
            Route::get('/index')->action([UserController::class, 'index'])->name('user.index'),
            Route::get('/timeSheet')->action([TimeSheetController::class, 'index'])->name('timeSheet.index'),
            Group::create()->middleware(AdminMiddleware::class)->routes(
                Route::get('/show/{user:\d+}')->action([UserController::class, 'show'])->name('user.show'),
                Route::get('/create')->action([UserController::class, 'create'])->name('user.create'),
                Route::post('/store')->action([UserController::class, 'store'])->name('user.store'),
                Route::get('/edit/{user:\d+}')->action([UserController::class, 'edit'])->name('user.edit'),
                Route::post('/update/{user:\d+}')->action([UserController::class, 'update'])->name('user.update'),
                Route::delete('/delete/{user:\d+}')->action([UserController::class, 'destroy'])->name('user.destroy'),
            ),
        ),
        Route::get('/login')->action([AuthController::class, 'login'])->name('user.login'),
        Route::post('/login')->action([AuthController::class, 'loginPost'])->name('user.login.post'),
        Route::get('/register')->action([AuthController::class, 'register'])->name('user.register'),
        Route::post('/register')->action([AuthController::class, 'registerPost'])->name('user.register.post'),
    ),
    Group::create()->middleware(Authentication::class)->routes(
        Route::post('/autocompleteProvider')->action([ListManagerController::class, 'fetch'])->name('autocomplete.provider'),
        Route::get('/')->action([DashboardController::class, 'index'])->name('home'),
        Group::create('/dashboard')->routes(
            Route::get('/')->action([DashboardController::class, 'index'])->name('dashboard.index'),
            Route::get('/tasks')->action([DashboardController::class, 'tasks'])->name('dashboard.tasks'),
        ),
        Group::create('/project')->routes(
            Route::get('/index')->action([ProjectController::class, 'index'])->name('project.index'),
            Route::get('/show/{project:\d+}')->action([ProjectController::class, 'show'])->name('project.show'),
            Group::create()->middleware(AdminMiddleware::class)->routes(
                Route::get('/create')->action([ProjectController::class, 'create'])->name('project.create'),
                Route::post('/store')->action([ProjectController::class, 'store'])->name('project.store'),
                Route::get('/edit/{project:\d+}')->action([ProjectController::class, 'edit'])->name('project.edit'),
                Route::post('/update/{project:\d+}')->action([ProjectController::class, 'update'])->name('project.update'),
                Route::delete('/delete/{project:\d+}')->action([ProjectController::class, 'destroy'])->name('project.destroy'),
            ),
            Route::get('/user/create/{project:\d+}')->action([ProjectUserController::class, 'create'])->name('project.user.create'),
            Route::post('/user/store/{project:\d+}')->action([ProjectUserController::class, 'store'])->name('project.user.store'),
            Route::delete('/user/delete/{user:\d+}')->action([ProjectUserController::class, 'destroy'])->name('project.user.destroy'),
        ),
        Group::create('/task')->routes(
            Route::get('/index')->action([TaskController::class, 'index'])->name('task.index'),
            Route::get('/show/{task:\d+}')->action([TaskController::class, 'show'])->name('task.show'),
            Route::get('/create')->action([TaskController::class, 'create'])->name('task.create'),
            Route::post('/store')->action([TaskController::class, 'store'])->name('task.store'),
            Route::get('/edit/{task:\d+}')->action([TaskController::class, 'edit'])->name('task.edit'),
            Route::post('/update/{task:\d+}')->action([TaskController::class, 'update'])->name('task.update'),
            Route::delete('/delete/{task:\d+}')->action([TaskController::class, 'destroy'])->name('task.destroy'),
            Route::post('/take/{task:\d+}')->action([TaskController::class, 'take'])->name('task.take'),
            Route::post('/complete/{task:\d+}')->action([TaskController::class, 'complete'])->name('task.complete'),
            Route::post('/return/{task:\d+}')->action([TaskController::class, 'return'])->name('task.return'),
            Group::create('/time-entry')->routes(
                Route::get('/index/{task:\d+}')->action([TaskTimeEntryController::class, 'index'])->name('taskTimeEntry.index'),
                Route::get('/create/{task:\d+}')->action([TaskTimeEntryController::class, 'create'])->name('taskTimeEntry.create'),
                Route::post('/store/{task:\d+}')->action([TaskTimeEntryController::class, 'store'])->name('taskTimeEntry.store'),
                Route::get('/edit/{time:\d+}')->action([TaskTimeEntryController::class, 'edit'])->name('taskTimeEntry.edit'),
                Route::post('/update/{time:\d+}')->action([TaskTimeEntryController::class, 'update'])->name('taskTimeEntry.update'),
                Route::delete('/delete/{time:\d+}')->action([TaskTimeEntryController::class, 'destroy'])->name('taskTimeEntry.destroy'),
            ),
            Group::create('/comment')->routes(
                Route::get('/index/{task:\d+}')->action([TaskCommentController::class, 'index'])->name('taskComment.index'),
                Route::post('/store/{task:\d+}')->action([TaskCommentController::class, 'store'])->name('taskComment.store'),
                Route::get('/edit/{comment:\d+}')->action([TaskCommentController::class, 'edit'])->name('taskComment.edit'),
                Route::post('/update/{comment:\d+}')->action([TaskCommentController::class, 'update'])->name('taskComment.update'),
                Route::delete('/delete/{comment:\d+}')->action([TaskCommentController::class, 'destroy'])->name('taskComment.destroy'),
            ),
        ),
    ),
];
