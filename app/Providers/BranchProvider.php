<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BranchProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        \App\Services\Branch\BranchServices::class => \App\Services\Branch\BranchImpl::class,
    ];

    public function provides(): array
    {
        return [
            \App\Services\Branch\BranchServices::class,
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
