<?php

namespace App\Providers;

use App\Repositories\Task\TaskEloquentRepository;
use App\Repositories\Task\TaskInterfaceRepository;
use App\Repositories\TaskList\TaskListEloquentRepository;
use App\Repositories\TaskList\TaskListInterfaceRepository;
use App\Repositories\User\UserEloquentRepository;
use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterfaceRepository::class, UserEloquentRepository::class);
        $this->app->bind(TaskListInterfaceRepository::class, TaskListEloquentRepository::class);
        $this->app->bind(TaskInterfaceRepository::class, TaskEloquentRepository::class);
    }
}
