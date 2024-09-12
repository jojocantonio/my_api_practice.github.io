<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\EventRepositoryInterface;
use App\Repository\EventReposiotry;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
