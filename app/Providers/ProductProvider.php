<?php

namespace App\Providers;

use App\Services\Products\ProductImpl;
use App\Services\Products\ProductUnitImpl;
use App\Services\Products\ProductUnitServices;
use App\Services\Products\ProductServices;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ProductProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        ProductServices::class => ProductImpl::class,
        ProductUnitServices::class => ProductUnitImpl::class,
    ];
    public function provides(): array
    {
        return [ProductServices::class, ProductUnitServices::class];
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
