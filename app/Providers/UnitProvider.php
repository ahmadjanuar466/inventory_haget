<?php

namespace App\Providers;

use App\Services\Units\UnitImpl;
use App\Services\Units\UnitServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class UnitProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        UnitServices::class => UnitImpl::class,
    ];

    public function provides(): array
    {
        return [
            UnitServices::class,
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
