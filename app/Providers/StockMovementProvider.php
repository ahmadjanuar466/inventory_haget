<?php

namespace App\Providers;

use App\Services\Inventory\Stock\StockMovementImpl;
use App\Services\Inventory\Stock\StockMovementServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class StockMovementProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [StockMovementServices::class => StockMovementImpl::class];
    public function provides(): array
    {
        return [StockMovementServices::class];
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
