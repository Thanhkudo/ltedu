<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Repositories\Contracts\ClassRepositoryInterface;
use App\Repositories\Contracts\ExerciseRepositoryInterface;
use App\Repositories\StudentRepository;
use App\Repositories\ClassRepository;
use App\Repositories\ExerciseRepository;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Bind Repository interfaces tới implementations cụ thể
        // Giúp dễ swap implementation mà không thay đổi code ở Service/Controller
        $this->app->bind(StudentRepositoryInterface::class, StudentRepository::class);
        $this->app->bind(ClassRepositoryInterface::class, ClassRepository::class);
        $this->app->bind(ExerciseRepositoryInterface::class, ExerciseRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}
