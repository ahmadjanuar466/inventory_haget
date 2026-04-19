<?php

namespace App\Providers;

use App\Services\Categories\CategoryImpl;
use App\Services\Categories\CategoryServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class CategoryProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        CategoryServices::class => CategoryImpl::class,
    ];

    public function provides(): array
    {
        return [
            CategoryServices::class,
        ];
    }

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
