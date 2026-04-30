<?php

namespace App\Providers;

use App\Services\Inventory\Stock\StockImpl;
use App\Services\Inventory\Stock\StockServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class StockProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        StockServices::class => StockImpl::class,
    ];
    public function provides(): array
    {
        return array_keys($this->bindings);
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
