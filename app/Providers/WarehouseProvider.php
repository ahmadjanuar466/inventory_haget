<?php

namespace App\Providers;

use App\Services\Warehouse\WarehouseImpl;
use App\Services\Warehouse\WarehouseServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class WarehouseProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        WarehouseServices::class => WarehouseImpl::class,
    ];

    public function provides(): array
    {
        return [
            WarehouseServices::class,
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
