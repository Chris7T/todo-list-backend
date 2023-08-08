<?php

namespace App\Providers;

use App\Repositories\User\UserEloquentRepository;
use App\Repositories\User\UserInterfaceRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserInterfaceRepository::class, UserEloquentRepository::class);
    }
}
