<?php

namespace App\Providers;

use App\Services\ProductPrice\ProductPriceImpl;
use App\Services\ProductPrices\ProductPriceServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ProductPriceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [ProductPriceServices::class => ProductPriceImpl::class];
    public function provides(): array
    {
        return [ProductPriceServices::class];
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
